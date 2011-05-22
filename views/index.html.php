<?php defined('BASEPATH') or die('No direct script access allowed') ?>
<html>
    <head>
        <title>CodeIgniter Migration Tool</title>
        <script type="text/javascript"><?php echo $jquery ?></script>
        <style type="text/css"><?php echo $style ?></style>
        <script type="text/javascript">
          var utc_string_to_date = function (utc) {
              var date = new Date();
              var raw_date = {
                year:   parseInt(utc.substr(0,4), 10),
                month:  parseInt(utc.substr(4,2), 10) - 1,
                day:    parseInt(utc.substr(6,2), 10),
                hour:   parseInt(utc.substr(8,2), 10),
                minute: parseInt(utc.substr(10, 2), 10),
                second: parseInt(utc.substr(12, 2), 10),
              };
              console.log(raw_date);
              date.setUTCFullYear(raw_date.year, raw_date.month, raw_date.day);
              date.setUTCHours(raw_date.hour, raw_date.minute, raw_date.second);

              return date;
          };
          var format_date = function (date) {
            var format_hours = function (hours) {
              hours = hours + 1;
              if (hours > 12) {
                return hours - 12;
              } else {
                return hours;
              }
            };
            var to_sixty = function (d) {
              if (d < 10) {
                return "0" + d;
              } else {
                return d;
              }
            };
            var am_or_pm = function (d) {
              if (d >= 12) {
                return 'PM';
              } else {
                return 'AM';
              }
            };
            var day_names = [
              'Sunday',
              'Monday',
              'Tuesday',
              'Wednesday',
              'Thursday',
              'Friday',
              'Saturday'
            ];
            var months = [
              'January',
              'February',
              'March',
              'April',
              'May',
              'June',
              'July',
              'August',
              'September',
              'October',
              'November',
              'December'
            ];

            return '' +
              day_names[date.getDay()] + ', ' +
              months[date.getMonth()] + ' ' +
              date.getDate() + ', ' +
              date.getFullYear() + ' at ' +
              format_hours(date.getHours()) + ':' +
              to_sixty(date.getMinutes()) + ':' +
              to_sixty(date.getSeconds()) + ' ' +
              am_or_pm(date.getHours());
          };
          $(document).ready(function () {
            $.each($('.time-display'), function (index, element) {
              var $this = $(this);
              var classes = $this.attr('class').split(' ');
              var time = null;
              $.each(classes, function (index, klass) {
                if (klass.indexOf('time-is-') === 0) {
                  time = klass.replace('time-is-', '');
                }
              });

              var utc_date = utc_string_to_date(time);
              $this.html(format_date(utc_date));
            });
          });
        </script>
    </head>
    <body>
        <table id="main-table">
            <tr id="header-row"> <td colspan="2" id="header"> CodeIgniter Migration Tool </td> </tr>
            <tr>
                <td><?php echo $left_column ?></td>
                <td><?php echo $right_column ?></td>
            </tr>
        </table>
    </body>
</html>
