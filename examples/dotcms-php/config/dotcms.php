<?php
use Dotcms\PhpSdk\Config\Config;

return new Config(
    host: 'https://demo.dotcms.com',
    apiKey: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhcGlhZmFhNzM1NC04OWFmLTRiYzYtYjcwNC03YjU1ZmM3ZjRlZGIiLCJ4bW9kIjoxNzQ2NDUwMDYzMDAwLCJuYmYiOjE3NDY0NTAwNjMsImlzcyI6IjYxZDZkZDBjNTciLCJleHAiOjE3NDczMTQwNjMsImlhdCI6MTc0NjQ1MDA2MywianRpIjoiNzJkZThhM2EtYzBmNC00ZDg3LWE2ODQtYWI2NjU5NWY4OTBhIn0.0npa2o30ZZeOV_E4h80-bAPHJt7AKBHjphMI5J3elT8',
    clientOptions: [
        'timeout' => 30,
        'verify' => true,
    ]
);
?>
