import json
import re
import sys
from os import path
import traceback as tb

from typing import Any

# https://regex101.com/r/kgCV7K
MOODLESTRUCT_REGEX = r"(?:['\"](\w+)['\"]\s*=>|return)\s*new\s*" \
    r"external_value\s*\(\s*(PARAM_\w+)\s*,\s*((?:(['\"]).+?\4)(?:\s*\.\s*(?:\w+::format\(\))|'.*?')*)" \
    r"(?:,\s*(\w+)(?:,\s*([^,]+?)(?:,\s*(\w+),?)?)?)?\s*\)"

HAS_WARNED = False
CURRENT_SERVICE: str | None = None

def warn(msg: str, *context: Any):
    """Prints a warning message to the console and sets the global HAS_WARNED variable to True.

    :param str msg: The warning message to print.
    """
    global HAS_WARNED
    WARN = "\033[43m\033[30mWARN:\033[0m "
    WARN_TAB = "    \033[43m\033[33m|\033[0m "

    HAS_WARNED = True

    stack = tb.extract_stack()
    stack_str = " -> ".join([f"\033[34m{frame.name}\033[0m" for frame in stack if frame != stack[-1]])

    service_msg: str
    if CURRENT_SERVICE is None:
        service_msg = "outside any service"
    else:
        service_msg = f"in service \033[36m{CURRENT_SERVICE}\033[0m"

    print(
        f"{WARN}\033[31m{msg}\033[0m {service_msg} ({stack_str})",
        f"\n{WARN_TAB}  " if len(context) > 0 else "",
        *[f"\033[2m{c}\033[0m".replace('\n', '\n\033[0m' + WARN_TAB + "  \033[2m") for c in context],
        file=sys.stderr,
        sep=""
    )

def convert_php_type_to_normal_type(param_type: str) -> str:
    CONVERSIONS = {
        "PARAM_INT": "int",
        "PARAM_TEXT": "String",
        "PARAM_URL": "String",
        "PARAM_BOOL": "bool",
    }

    return CONVERSIONS.get(param_type, param_type)

def explain_php_value(val: str) -> tuple[None | str | int | bool, str]:
    SPECIAL_VALUES = {
        "$USER->id": ("current userid", "int"),
        "null": (None, "")
    }

    if val in SPECIAL_VALUES.keys():
        return SPECIAL_VALUES[val]
    elif val.isnumeric():
        return (int(val), "int")
    elif val.lower() in ("true", "false"):
        return (val.lower() == "true", "bool")
    elif val[0] + val[-1] in ("''", '""'):

        # see if the same kind of quote is within string
        if val[1:-1].count(val[0]) > 0:
            warn("found potentially non-literal string", val)

        return (val[1:-1], "String")
    else:
        warn("found unknown value", val)
        return (f"unknown: {val}", "unknown")

def parse_isrequired(inpot: str) -> bool | None:
    if inpot in ('VALUE_REQUIRED', ''):
        return True
    elif inpot == 'VALUE_DEFAULT':
        return False
    else:
        warn("found unparseable value for isrequired", inpot)
        return None

def parse_nullable(inpot: str) -> bool | None:
    if inpot in ('', 'NULL_NOT_ALLOWED'):
        return False
    elif inpot == 'NULL_ALLOWED':
        return True
    else:
        warn(f"found weird value for nullable: {inpot}")
        return None

class SlotsDict:
    @property
    def __dict__(self):
        slots = tuple()

        for cls in self.__class__.__mro__:
            if cls != SlotsDict and issubclass(cls, SlotsDict):
                slots = cls.__slots__ + slots
        return {name: self.__getattribute__(name) for name in slots}

class ReturnInfo(SlotsDict):
    __slots__ = ('type', 'description', 'nullable')

    def __init__(self, type: str, description: str, nullable: str):
        self.type = convert_php_type_to_normal_type(type)
        self.description = description
        self.nullable = parse_nullable(nullable)

class ParamInfo(SlotsDict):
    __slots__ = ('type', 'description', 'required', 'default_value', 'nullable')

    def __init__(self,
                 type: str,
                 description: str,
                 required: str,
                 default_value: str,
                 nullable: str):

        self.type = convert_php_type_to_normal_type(type)

        defval, deftype = explain_php_value(default_value)
        if defval is not None and deftype != self.type:
            warn(f"Type of default value does not match parameter type - {deftype} != {self.type}", description)

        self.description = description
        self.required = parse_isrequired(required)
        self.default_value = defval
        self.nullable = parse_nullable(nullable)

class FunctionInfo(SlotsDict):
    __slots__ = ('name', 'group', 'capabilities', 'description', 'path')

    def __init__(self, name: str, group: str, capabilities: list[str], description: str, path: str):
        self.name = name
        self.group = group
        self.capabilities = capabilities
        self.description = description
        self.path = path

class FunctionInfoEx(FunctionInfo):
    __slots__ = ('parameters', 'returns', 'returns_multiple')

    def __init__(self,
                 parent: FunctionInfo,
                 parameters: dict[str, ParamInfo],
                 returns: dict[str, ReturnInfo],
                 returns_multiple: bool):
        super().__init__(**parent.__dict__)

        self.parameters = parameters
        self.returns = returns
        self.returns_multiple = returns_multiple

def extract_function_info(file_content: str) -> list[FunctionInfo]:
    function_info = []

    # Removing comments, PHP tags, and definitions
    clean_content = re.sub(r"//.*|<\?php|defined\(.*\)\s*\|\|\s*die\(\);", "", file_content)

    # Splitting the content based on function definition blocks
    # https://regex101.com/r/qyzYks
    functions = re.findall(r"'(local_lbplanner_(\w+?)_(\w+))' => \[(.*?)\],", clean_content, re.DOTALL)

    for function in functions:
        func_dict = {}

        # Extracting function name and group
        func_dict["name"] = function[2]
        func_dict["group"] = function[1]

        # Extracting and adjusting capabilities
        capabilities = re.search(r"'capabilities' => '.*:(.*?)'", function[3])
        if capabilities is None:
            # check if call needs no capabilities
            capabilities = re.search(r"'capabilities' => ''", function[3])
            func_dict["capabilities"] = [] if capabilities else None
        else:
            func_dict["capabilities"] = [cap.strip() for cap in capabilities.group(1).split(',') if len(cap) > 0]

        # Extracting description
        description = re.search(r"'description' => '(.*?)'", function[3])
        func_dict["description"] = description.group(1) if description else None

        # Extracting and adjusting path
        classpath = re.search(r"'classpath' => 'local/(.*?)'", function[3])
        func_dict["path"] = classpath.group(1) if classpath else None

        # Only adding to the list if all information is present
        if all(value is not None for value in func_dict.values()):
            function_info.append(FunctionInfo(**func_dict))
        else:
            warn(f"Could not gather all info for {func_dict["name"]}", func_dict)

    if len(function_info) == 0:
        warn("Couldn't find any functions!")

    return function_info


def extract_php_functions(php_code: str, name: str) -> tuple[str | None, str | None]:
    # Regular expression to match the function names and bodies
    # https://regex101.com/r/9GtIMA
    pattern = re.compile(r"(public static function (\w+_(?:returns|parameters))\W[^{}]*?{[^{}]+?})", re.DOTALL)

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
        warn(f"Couldn't find parameters function in {name}")
    if returns_function is None:
        warn(f"Couldn't find returns function in {name}")

    return parameters_function, returns_function

def parse_imports(input_str: str, symbol: str) -> str | None:
    use_pattern = fr"use ((?:\w+\\)+){symbol};"
    uses: list[str] = re.findall(use_pattern, input_str)

    # TODO: actually separate namespaces, check for unknown ones
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
        warn("found import collision?", input_str)
        return None
    elif len(fp_l) == 0:
        warn(f"Couldn't find symbol: {symbol}", input_str)
        return None
    else:
        return fp_l[0]

def parse_phpstuff(inpot: str) -> str:
    # https://regex101.com/r/p5FzCh
    casepattern = r"const (\w+) = (\d+|true|false|(['\"]).*?\3)"

    if inpot.endswith('::format()'):
        enum_name = inpot[:-10]
        fullbody_pattern = f"class {enum_name} extends Enum {{.*?}}"

        fp = f"lbplanner/classes/enums/{enum_name}.php"
        if not path.exists(fp):
            warn(f"Couldn't find enum file {fp}")
            return ""
        with open(fp, "r") as f:
            matches: list[str] = re.findall(fullbody_pattern, f.read(), re.DOTALL)
            if len(matches) == 1:
                body = matches[0]
            else:
                warn(f"couldn't parse enum {enum_name}", matches)

        cases = {}
        matches = re.findall(casepattern, body)
        for match in matches:
            # capitalizing first letter, if exists
            name = "".join([match[0][0].upper(), match[0][1:].lower()])
            cases[name] = match[1].replace("'", '"')

        return "{ " + ", ".join([f"{name} = {value}" for name, value in cases.items()]) + " }"
    else:
        warn('unknown phpstuff', inpot)
        return ""

def parse_phpstring(inpot: str) -> str:
    WHITESPACE = '. \t\n\r' # the . is for string concat in php

    out = []
    strlit = False
    quotetype = ''
    tmp_refarr: list[str] = []
    for char in inpot:
        if char in '\'"':
            if not strlit:
                strlit = True
                quotetype = char
                if len(tmp_refarr) > 0:
                    out.append(parse_phpstuff("".join(tmp_refarr)))
                    tmp_refarr = []
            else:
                if char == quotetype:
                    strlit = False
                else:
                    out.append(char)
        else:
            if strlit:
                out.append(char)
            else:
                if char in WHITESPACE:
                    continue
                else:
                    tmp_refarr.append(char)

    if len(tmp_refarr) > 0:
        out.append(parse_phpstuff("".join(tmp_refarr)))
        tmp_refarr = []

    return "".join(out)

def parse_returns(input_str: str, file_content: str, name: str) -> tuple[dict[str, ReturnInfo], bool]:
    # https://regex101.com/r/gUtsX3/
    redir_pattern = r"(\w+)::(\w+)(?<!format)\(\)"
    # https://regex101.com/r/rq5q6w/
    nullensure_pattern = r".*?{\s*return null;?\s*}"

    # Check for the presence of 'external_multiple_structure'
    is_multiple_structure = "external_multiple_structure" in input_str

    redir_matches: list[list[str]] = re.findall(redir_pattern, input_str)
    if len(redir_matches) > 1:
        warn(f"Couldn't parse return values in {name}", input_str)
        return ({}, False)

    if len(redir_matches) == 1:
        match = redir_matches[0]
        meth_pattern = rf"public static function {match[1]}\(\)(?: ?: ?\w+)? ?{{(?P<body>.*?)}}"

        fp = parse_imports(file_content, match[0])
        if fp is None:
            # already warned in parse_imports, we don't need to warn again
            return {}, is_multiple_structure

        with open(fp, "r") as f:
            new_file_content = f.read()
            meth_matches: list[str] = re.findall(meth_pattern, new_file_content, re.DOTALL)
            if len(meth_matches) == 0:
                warn(f"Couldn't find {match[0]}::{match[1]}() inside {fp} for {name}")
                return ({}, False)
            elif len(meth_matches) > 1:
                raise Exception(f"Found multiple definitions for {match[0]}::{match[1]}() inside {fp}")
            else:
                result = parse_returns(meth_matches[0], new_file_content, fp)

                # if multiple_structure is detected here, add it
                if is_multiple_structure:
                    return (result[0], True)
                else:
                    return result

    matches: list[list[str]] = re.findall(MOODLESTRUCT_REGEX, input_str)

    output_dict = {}
    for match in matches:
        key = match[0]
        if key is None:
            if len(matches) > 1:
                warn("got empty return key name in a structure larger than 1", matches)
            else:
                key = ''

        if not parse_isrequired(match[4]):
            warn(f"found optional value in returns structure for {name}", input_str)

        default_str = match[5]
        if default_str not in ('null', ''):
            warn(f"found non-null 'default value' in returns structure for {name}: {default_str}", input_str)

        output_dict[key] = ReturnInfo(match[1], parse_phpstring(match[2]), match[6])

    if len(output_dict) == 0:
        if re.match(nullensure_pattern, input_str) is None:
            warn(f"could not find any returns in non-empty {name}", input_str)

    return output_dict, is_multiple_structure

def parse_params(input_text: str) -> dict[str, ParamInfo]:
    # Regular expression to match the parameters inside the 'new external_value()' function

    # Find all matches of the pattern in the input text
    matches: list[list[str]] = re.findall(MOODLESTRUCT_REGEX, input_text)

    if len(matches) == 0:
        nullensure_pattern = r".*return new external_function_parameters(\s*\[\]\s*);.*"
        if re.match(nullensure_pattern, input_text) is not None:
            warn("could not parse params", input_text)
        return {}

    result = {}
    for match in matches:
        param_name = match[0]
        result[param_name] = ParamInfo(
            match[1],
            parse_phpstring(match[2]),
            match[4],
            match[5],
            match[6],
        )

    return result


if __name__ == "__main__":
    with open("lbplanner/db/services.php", "r") as file:
        content = file.read()
        infos = extract_function_info(content)

        complete_info = []

        for i, info in enumerate(infos):

            CURRENT_SERVICE = info.name

            with open(info.path, "r") as func_file:
                func_content = func_file.read()
                params_func, returns_func = extract_php_functions(func_content, info.path)

                if returns_func is None or params_func is None:
                    continue

                returns, returns_multiple = parse_returns(returns_func, func_content, info.path)

                params = parse_params(params_func)

                complete_info.append(FunctionInfoEx(info, params, returns, returns_multiple))

        CURRENT_SERVICE = None

        data = json.dumps(complete_info, default=lambda x: x.__dict__)

        if sys.argv[1] == "-":
            print(data)
        else:
            declaration = f"const funcs = {data}"

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

    if HAS_WARNED:
        sys.exit(1)
