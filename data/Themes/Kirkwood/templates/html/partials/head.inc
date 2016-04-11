<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= FN1_URL; ?>/css/default.css" />
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL; ?>/css/local/screen.css" />
    <link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/css/Font-Awesome/css/font-awesome.css" />
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700,600" rel="stylesheet" type="text/css">
    <?php
        if (defined('THEME')) {
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".BASE_URI.'/'.THEME."/css/screen.css\" />";
        }
    ?>
    <script type="text/javascript">
        var APPLICATION = {
            BASE_URL: '<?= BASE_URL; ?>'
        };
    </script>
</head>
