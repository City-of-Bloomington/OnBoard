<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $this->_include('partials/stylesheets.inc'); ?>
    <title><?= isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
    <script type="text/javascript" src="<?= BASE_URI; ?>/js/onboard-<?= VERSION; ?>.js">
    </script>
    <script type="text/javascript">
        ONBOARD.BASE_URL = '<?= BASE_URL; ?>';
        ONBOARD.BASE_URI = '<?= BASE_URI; ?>';
    </script>
</head>
