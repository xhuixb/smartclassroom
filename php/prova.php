


<!DOCTYPE html>
<html>
    <body>

        <?php
        $date1 = date_create("2013-03-15");
        $date2 = date_create("2013-03-15");
        $diff = date_diff($date2, $date1);
        $xx= intval($diff->format("%R%a"));
        echo $xx;
        ?>

    </body>
</html>