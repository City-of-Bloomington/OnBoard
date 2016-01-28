<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?php echo FN1_URL; ?>/css/default.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/local/layouts/<?php echo $this->layout; ?>.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/local/screen.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/css/Font-Awesome/css/font-awesome.css" />
    <script type="text/javascript">
        var APPLICATION = {
            BASE_URL: '<?php echo BASE_URL; ?>'
        };
    </script>
</head>
