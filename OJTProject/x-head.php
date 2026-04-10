<?php 
include('./functions/auth.php');
include('./functions/dbFunctions.php');
?>

<link href='./icons/bootstrap-5.3.8-dist/css/bootstrap.min.css' rel='stylesheet'>
<link href='./icons/bootstrap-icons-1.13.1/bootstrap-icons.min.css' rel='stylesheet'>
<link href='./icons/datatables/datatables.min.css' rel='stylesheet'>
<script src='./icons/jquery-4.0.0.min.js'></script>
<script src='./icons/datatables/datatables.min.js'></script>
<script src='./icons/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js'></script>
<link href='./icons/fontawesome-free-7.2.0-web/css/all.min.css' rel='stylesheet'>
<script src='./icons/sweetalert2.all.min.js'></script>
<style>
    @font-face {
            font-family: 'Futura Custom';
            src: url('./icons/Fotura\ Font/Futura LT Bold Oblique.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }
    @font-face {
    font-family: 'Futur';
    src: url('./icons/Fotura Font/Futura-Bold.woff2') format('woff2'),
        url('./icons/Fotura Font/Futura-Bold.woff') format('woff');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}
</style>
<?php include('./ajax/x-modalinator.php')?>