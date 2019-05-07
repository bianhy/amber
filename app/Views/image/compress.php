<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP上传图片自动压缩</title>
</head>

<body>
<img src="<?php echo $image; ?>"><br><br>
<form enctype="multipart/form-data" method="post" action="">
<br>
<input type="file" name="image" size="50" value="浏览"><p>
生成缩略图宽度：<input type="text" name="width" size="5"><p>
生成缩略图长度：<input type="text" name="length" size="5"><p>
<input type="submit" value="上传图片">
</form>
</body>
</html>