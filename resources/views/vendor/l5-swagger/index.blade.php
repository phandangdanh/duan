<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>L5 Swagger UI</title>
  <link rel="stylesheet" type="text/css" href="{{ asset('docs/asset/swagger-ui.css') }}">
  <link rel="icon" type="image/png" href="{{ asset('docs/asset/favicon-32x32.png') }}" sizes="32x32"/>
  <link rel="icon" type="image/png" href="{{ asset('docs/asset/favicon-16x16.png') }}" sizes="16x16"/>
  <style>
    html { box-sizing: border-box; overflow-y: scroll; }
    *, *:before, *:after { box-sizing: inherit; }
    body { margin:0; background: #fafafa; }
  </style>
  @if (config('l5-swagger.defaults.proxy'))
    <script>
      // Allow Swagger UI to run behind proxies if needed
      window.onload = function () { window.ui && window.ui.getConfigs && window.ui.getConfigs().url = '{{ url('docs?api-docs.json') }}'; };
    </script>
  @endif
</head>
<body>
<div id="swagger-ui"></div>
<script src="{{ asset('docs/asset/swagger-ui-bundle.js') }}"></script>
<script src="{{ asset('docs/asset/swagger-ui-standalone-preset.js') }}"></script>
<script>
  window.onload = function() {
    const ui = SwaggerUIBundle({
      dom_id: '#swagger-ui',
      url: "{{ url('docs?api-docs.json') }}",
      operationsSorter: null,
      validatorUrl: null,
      oauth2RedirectUrl: "{{ url('api/oauth2-callback') }}",
      presets: [ SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset ],
      plugins: [ SwaggerUIBundle.plugins.DownloadUrl ],
      layout: "StandaloneLayout",
      docExpansion : "none",
      deepLinking: true,
      filter: true,
      persistAuthorization: "false",
      // Custom tag order: Location -> Users -> Categories
      tagsSorter: function(a, b) {
        const order = ['Location', 'Users', 'Categories'];
        const ia = order.indexOf(a), ib = order.indexOf(b);
        if (ia === -1 && ib === -1) return a.localeCompare(b);
        if (ia === -1) return 1; if (ib === -1) return -1;
        return ia - ib;
      }
    })
    window.ui = ui
  }
</script>

</body>
</html>


