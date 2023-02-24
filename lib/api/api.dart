part of lbplanner_engine;

/// Provides basic methods to communicate with the moodle API
class Api {
  /// The root url of the server.
  static const serverRoot = "https://elearning.tgm.ac.at";
  //static const serverRoot = kReleaseMode ? "https://elearning.tgm.ac.at" : "https://projekte.tgm.ac.at/lbplaner4";

  /// The base URL of the API
  static const apiEndpoint = "$serverRoot/webservice/rest/server.php";

  /// The format the api should use.
  static const format = "json";

  /// Content-Type for post requests.
  static const postFormat = "application/x-www-form-urlencoded";

  /// The headers for a post request.
  static const postHeaders = {"Content-Type": postFormat};

  /// Client used to communicate with the API
  static final http.Client client = http.Client();

  /// How long it takes for a request to time out.
  static const requestTimeout = Duration(seconds: 10);

  /// Sets the timout callback for the given future using [requestTimeout] as duration and returns [Response.timeout()] when the future times out.
  static Future<http.Response> timout(Future<http.Response> f) async {
    return f.timeout(requestTimeout, onTimeout: () => Response.timeout());
  }

  /// Sends a request to the API.
  /// - [functionName] The name of the function to call
  /// - [params] The parameters to pass to the function
  /// - [token] The token to use for authentication
  /// - [body] If this is set the request will be a POST request with the given body
  /// - [reportError] Whether to report errors using [onError]
  static Future<RawApiResponse> makeRequest({required String functionName, required String token, Map<String, dynamic>? params, Map<String, dynamic>? body, bool reportError = true}) async {
    var url = "$apiEndpoint?moodlewsrestformat=$format&wstoken=$token&wsfunction=$functionName";

    var func = "$functionName(${params ?? ''})";

    log("Calling $func ...", LogTypes.tracking);

    if (params != null) {
      url += "&" + params.entries.map((e) => "${e.key}=${e.value != null ? Uri.encodeComponent(e.value.toString()) : null}").join("&");
    }

    var uri = Uri.parse(url);

    if (body != null) {
      for (var key in body.keys) {
        var value = body[key];
        body[key] = value != null ? Uri.encodeComponent(value.toString()) : null;
      }
    }

    var response = await timout(body != null ? client.post(uri, body: body, headers: postHeaders) : client.get(uri));

    var result = RawApiResponse(response);

    _logResponse(result, func);

    if (result.failed && reportError) onError?.call(result);

    return result;
  }

  /// Requests a token for the given [service] with the given [username] and [password].
  static Future<ApiResponse<String>> requestToken(String password, String username, ApiServices service) async {
    log("Requesting token for ${service.name}...", LogTypes.tracking);

    var url = "$serverRoot/login/token.php?service=${service.name}&moodlewsrestformat=$format";

    var response = await timout(client.post(Uri.parse(url), body: {"username": username, "password": password}, headers: postHeaders));

    var json = jsonDecode(response.body);

    var result = ApiResponse<String>(response, json["token"]);

    _logResponse(result, "${service.name} token");

    return result;
  }

  static void _logResponse(RawApiResponse response, String func) {
    if (response.response.request == null) return;

    var req = response.response.request!;

    log("Request: ${req.method} $func, Response: ${response.response.statusCode}${response.failed ? ", Message: '${response.errorMessage}'" : ""}", response.succeeded ? LogTypes.success : LogTypes.error, 1);
  }

  /// Callback called when an API call reults in an error.
  static void Function(RawApiResponse response)? onError;
}
