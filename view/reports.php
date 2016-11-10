<?
print_r($_GET);
include("header.php");?>
<form action="index.php?action=reports">
<input type="text" name="daterange" value="01/01/2015 1:30 PM - 01/01/2015 2:00 PM" />
<input type='submit'>
 </form>
<script type="text/javascript">
$(document).ready(function() {
$(function() {
    $('input[name="daterange"]').daterangepicker();
});
});
</script>