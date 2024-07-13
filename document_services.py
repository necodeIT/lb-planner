import json
import re
import sys
from os import path

WARN = "\033[43m\033[30mWARN:\033[0m "
WARN_TAB = "    \033[43m \033[0m "

def extract_function_info(file_content: str) -> list[dict[str, str | None]]:
    function_info = []

    # Removing comments, PHP tags, and definitions
    clean_content = re.sub(r"//.*|<\?php|defined\(.*\)\s*\|\|\s*die\(\);", "", file_content)

    # Splitting the content based on function definition blocks
    functions = re.findall(r"'(.*?)' => array\((.*?)\),", clean_content, re.DOTALL)

    for function in functions:
        func_dict = {}

        # Extracting function name and group
        func_name_match = re.match(r"local_lbplanner_(.*?)_(.*)", function[0])
        if func_name_match:
            func_dict["function_name"] = function[0]
            func_dict["group"] = func_name_match.group(1)
        else:
            continue

        # Extracting and adjusting capabilities
        capabilities = re.search(r"'capabilities' => '.*:(.*?)'", function[1])
        if capabilities is None:
            # check if call needs no capabilities
            capabilities = re.search(r"'capabilities' => ''", function[1])
            func_dict["capabilities"] = "" if capabilities else None
        else:
            func_dict["capabilities"] = capabilities.group(1)

        # Extracting description
        description = re.search(r"'description' => '(.*?)'", function[1])
        func_dict["description"] = description.group(1) if description else None

        # Extracting and adjusting path
        classpath = re.search(r"'classpath' => 'local/(.*?)'", function[1])
        func_dict["path"] = classpath.group(1) if classpath else None

        # Only adding to the list if all information is present
        if all(value is not None for value in func_dict.values()):
            function_info.append(func_dict)
        else:
            print(WARN, f"Could not gather all info for {func_dict["function_name"]}")
            print(WARN_TAB, func_dict)

    return function_info


def extract_php_functions(php_code: str, name: str) -> tuple[str | None, str | None]:
    # Regular expression to match the function names and bodies
    # https://regex101.com/r/9GtIMA
    pattern = re.compile(r"(public static function (\w+_(?:returns|parameters))[^\w].*?{.*?})", re.DOTALL)

    # Find all matches in the PHP code
    matches: list[tuple[str, str]] = pattern.findall(php_code)

    parameters_function = None
    returns_function = None

    for match in matches:
        # Extract function name
        function_name = match[1]

        if function_name.endswith("_parameters"):
            parameters_function = match[0]
        elif function_name.endswith("_returns"):
            returns_function = match[0]

    if parameters_function is None:
        print(WARN, f"Couldn't find parameters function in {name}")
    if returns_function is None:
        print(WARN, f"Couldn't find returns function in {name}")

    return parameters_function, returns_function

def parse_imports(input_str: str, symbol: str) -> str:
    use_pattern = fr"use ((?:\w+\\)+){symbol};"
    uses: list[str] = re.findall(use_pattern, input_str)

    namespaces = {
        "local_lbplanner": "classes"# not entirely true, but good enough for now
    }
    fp_l: list[str] = []
    for use in uses:
        p = use.split('\\')[:-1]

        namespace = namespaces.get(p[0])
        if namespace is not None:
            p[0] = namespace

        fp_l.append(path.join("lbplanner", *p, f"{symbol}.php"))

    if len(fp_l) > 1:
        raise Exception("found import collision?")
    elif len(fp_l) == 0:
        raise Exception(f"Couldn't find symbol: {symbol}")
    else:
        return fp_l[0]

def parse_returns(input_str: str, file_content: str, name: str):
    pattern = r"'(\w+)' => new external_value\((\w+), '([^']+)'"
    redir_pattern = r"(\w+)::(\w+)\(\)"

    matches = re.findall(redir_pattern, input_str)
    if len(matches) > 1:
        raise Exception(f"Couldn't parse return values in {name}")

    if len(matches) == 1:
        match = matches[0]
        meth_pattern = rf"public static function {match[1]}\(\)(?: ?: ?\w+)? ?{{(?P<body>.*?)}}"

        fp = parse_imports(file_content, match[0])
        with open(fp, "r") as f:
            new_file_content = f.read()
            matches = re.findall(meth_pattern, new_file_content, re.DOTALL)
            if len(matches) == 0:
                raise Exception(f"Couldn't find {match[0]}::{match[1]}() inside {fp}")
            elif len(matches) > 1:
                raise Exception(f"Found multiple definitions for {match[0]}::{match[1]}() inside {fp}")
            else:
                return parse_returns(matches[0], new_file_content, fp)

    matches = re.findall(pattern, input_str)

    output_dict = {}
    for match in matches:
        key, value_type, description = match
        output_dict[key] = {"type": convert_param_type_to_normal_type(value_type), "description": description}

    # Check for the presence of 'external_multiple_structure'
    is_multiple_structure = "external_multiple_structure" in input_str

    return output_dict, is_multiple_structure


def convert_param_type_to_normal_type(param_type: str) -> str:
    CONVERSIONS = {
        "PARAM_INT": "int",
        "PARAM_TEXT": "String",
        "PARAM_URL": "String",
        "PARAM_BOOL": "bool",
    }

    return CONVERSIONS.get(param_type, param_type)


def parse_params(input_text: str) -> dict[str, dict[str, str | bool | None]]:
    # Regular expression to match the parameters inside the 'new external_value()' function
    pattern = r"'(\w+)' => new external_value\s*\(\s*(\w+)\s*,\s*'([^']+)',\s*(\w+),\s*([\w\d]+|\w+),\s*(\w+)\s*\)"

    # Find all matches of the pattern in the input text
    matches = re.findall(pattern, input_text)

    result = {}
    for match in matches:
        param_name = match[0]
        result[param_name] = {
            "type": convert_param_type_to_normal_type(match[1]),
            "description": match[2],
            "required": True if match[3] == "VALUE_REQUIRED" else False,
            "default_value": match[4] if match[4] != "null" else None,
            "nullable": False if match[5] == "NULL_NOT_ALLOWED" else True,
        }

    return result


if __name__ == "__main__":
    with open("lbplanner/db/services.php", "r") as file:
        content = file.read()
        infos = extract_function_info(content)

        complete_info = []

        for i, info in enumerate(infos):
            if info["path"] is None:
                print(WARN, "skipped")
                continue
            with open(info["path"], "r") as func_file:
                func_content = func_file.read()
                params_func, returns_func = extract_php_functions(func_content, info["path"])

                if returns_func is None or params_func is None:
                    continue

                returns, returns_multiple = parse_returns(returns_func, func_content, info["path"])

                incomplete_info = info

                params = parse_params(params_func)

                incomplete_info["parameters"] = params
                incomplete_info["returns"] = returns
                incomplete_info["returns_multiple"] = returns_multiple

                complete_info.append(incomplete_info)

        declaration = f"const funcs = {json.dumps(complete_info)}"

        script: str
        with open(f"{sys.argv[1]}/script.js", "r") as f:
            script = f.read()
            lines = script.splitlines()
            for i in range(len(lines)):
                if lines[i].startswith('const funcs = '):
                    lines[i] = declaration
            script = "\n".join(lines)
        
        with open(f"{sys.argv[1]}/script.js", "w") as f:
            f.write(script)
