<?php
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$service} - 在线接口文档</title>

    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>

<body>

<br />
<style>
pre {
white-space: pre-wrap; /* css-3 */
white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
white-space: -pre-wrap; /* Opera 4-6 */
white-space: -o-pre-wrap; /* Opera 7 */
word-wrap: break-word; /* Internet Explorer 5.5+ */
}
</style>
<div class="container">

<div class="jumbotron">

EOT;

echo "<h2>接口：$service</h2><br/><p><strong>$description</strong><br/>$descComment</p><br/>";

echo <<<EOT
<h3>接口参数</h3>
<table class="table table-striped" >
<thead>
<tr><th>参数名字</th><th>类型</th><th>是否必须</th><th>默认值</th><th>其他</th><th>说明</th></tr>
EOT;

foreach ($rules as $key => $rule) {
    $name = $rule['name'];
    if (!isset($rule['type'])) {
        $rule['type'] = 'string';
    }
    $type = isset($typeMaps[$rule['type']]) ? $typeMaps[$rule['type']] : $rule['type'];
    $require = isset($rule['require']) && $rule['require'] ? '<font color="red">必须</font>' : '可选';
    $default = isset($rule['default']) ? $rule['default'] : '';
    if ($default === NULL) {
        $default = 'NULL';
    } else if (is_array($default)) {
        $default = json_encode($default);
    } else if (!is_string($default)) {
        $default = var_export($default, true);
    }

    $other = '';
    if (isset($rule['min'])) {
        $other .= ' 最小：' . $rule['min'];
    }
    if (isset($rule['max'])) {
        $other .= ' 最大：' . $rule['max'];
    }
    if (isset($rule['range'])) {
        $other .= ' 范围：' . implode('~', $rule['range']);
    }
    $desc = isset($rule['desc']) ? trim($rule['desc']) : '';

    echo "<tr><td>$name</td><td>$type</td><td>$require</td><td>$default</td><td>$other</td><td>$desc</td></tr>\n";
}

echo <<<EOT
</table>

<br>
EOT;
if ($example_url) {
    echo <<<EOT
<h3>请求示例</h3>
<table class="table table-striped" >
<thead>
<tr></tr>
EOT;
    echo '<tr><td><pre>' . $example_url . '</td></tr></pre>';
    echo "</table>";
}

if ( $example_ret) {
    echo <<<EOT
<h3>返回结果</h3>
<table class="table table-striped" >
<thead>
<tr></tr>
EOT;
    echo '<tr><td><pre>' . htmlspecialchars($example_ret) . '</td></tr></pre>';
    echo "</table>";
}
echo <<<EOT

<h3>返回结果说明</h3>
<table class="table table-striped" >
<thead>
<tr><th>返回字段</th><th>类型</th><th>说明</th></tr>
EOT;

foreach ($returns as $item) {
    $name = $item['name'];
    $type = isset($typeMaps[$item['type']]) ? $typeMaps[$item['type']] : $item[0];
    $detail = $item['desc'];

    echo "<tr><td>$name</td><td>$type</td><td>$detail</td></tr>";
}
echo <<<EOT
</table><br/>
    <div role="alert" class="alert alert-info">
      <strong>温馨提示：</strong> 此接口参数列表根据后台代码自动生成，可将 ?api= 改成您需要查询的接口/服务
    </div>

</div>

</div> <!-- /container -->

</body>
</html>
EOT;
