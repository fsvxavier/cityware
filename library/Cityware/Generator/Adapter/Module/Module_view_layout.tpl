<?php echo $this->doctype(); ?>
<html class="no-js" lang="pt-br">
    <head>
        <?php echo $this->headMeta(); ?>
        <?php echo $this->headTitle(); ?>
        <script type="text/javascript" src="<?php echo $this->urlDefault; ?>js/modernizr.custom.js"></script>
        <?php echo $this->headStyle(); ?>
        <?php echo $this->headLink(); ?>
    </head>
    <body<?php echo $this->extraValBody; ?>>
        <div class="fullcontainer">
        <?php echo $this->content; ?>
        </div>
        <script type="text/javascript">
            var URL_DEFAULT = "<?php echo $this->urlDefault; ?>";
            var LINK_DEFAULT = "<?php echo $this->linkDefault; ?>";
            var LINK_MODULE = "<?php echo $this->linkModule; ?>";
            var LINK_CONTROLLER = "<?php echo $this->linkController; ?>";
            var LINK_ACTION = "<?php echo $this->linkAction; ?>";
            var BASE_MODULE = "<?php echo $this->baseModule; ?>";
            var BASE_CONTROLLER = "<?php echo $this->baseController; ?>";
            var BASE_ACTION = "<?php echo $this->baseAction; ?>";
            var URL_IMG_LANG = "<?php echo $this->urlDefault; ?>images/<?php echo $this->baseModule; ?>/<?php echo $this->langDefault; ?>/";
            var URL_IMG = "<?php echo $this->urlDefault; ?>images/<?php echo $this->baseModule; ?>/";
            var URL_UPLOAD = "<?php echo $this->urlUpload; ?>";
            var AJAX_ACTION_DATAGRID = "<?php echo $this->ajaxActionDatagrid; ?>";
            var IS_MOBILE = "<?php echo $this->isMobile; ?>";
            var MOBILE_TYPE = "<?php echo $this->mobileType; ?>";
        </script>
        <?php echo $this->headScript(); ?>
        <?php echo $this->inlineScript(); ?>
    </body>
</html>