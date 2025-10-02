file lists bugs found on the app.

[ ] https://book.vai.me/admin/dashboard should be more comprehansive, display more reports and widget style links to pos, settigs and most inportant pages
[ ] https://book.vai.me/admin/staff/1/edit - staff edit page need to be developed. currenlty blank page is displayed with wrong header.
[ ] we are missing page to manage services and service categories
[ ] on https://book.vai.me/admin/waitlist - Illuminate\Database\QueryException SQLSTATE[42S02]: Base table or view not found: 1146 Table 'beauty_salon_management.waitlists' doesn't exist (Connection: mysql, SQL: select count(*) as aggregate from `waitlists`)
[ ] https://book.vai.me/admin/appointments/create revisit booking in admin, Available Time Slots seems empty after date selection, also if we could implement suggested times / multiple timeslots for quick selection, that would be SUPER!
[ ] https://book.vai.me/admin/pos/catalog there is  no way to add new products, we should be able to have products (with inventory and warehouse managment) and services in the pos.
[ ] https://book.vai.me/admin/settings should have more advanced functionality, like adding google analytics for example, managing translations and so on.
[ ] https://book.vai.me/admin/reports/clients - BadMethodCallException Method Illuminate\Database\Eloquent\Collection::paginate does not exist.
[ ] https://book.vai.me/admin/reports/staff - BadMethodCallException Method Illuminate\Support\Collection::paginate does not exist.
[ ] https://book.vai.me/admin/reports/business-intelligence - Illuminate\Database\QueryException SQLSTATE[42S22]: Column not found: 1054 Unknown column 'appointment_time' in 'field list' (Connection: mysql, SQL: select HOUR(appointment_time) as hour, COUNT(*) as count from `appointments` where `appointment_date` between 2025-10-01 00:00:00 and 2025-12-31 23:59:59 and `status` = completed group by `hour` order by `count` desc)  LARAVEL 12.32.5 PHP 8.3.14 UNHANDLED CODE 42S22
[ ] https://book.vai.me/admin/reports/appointments - Illuminate\Database\QueryException SQLSTATE[42S22]: Column not found: 1054 Unknown column 'appointment_time' in 'field list' (Connection: mysql, SQL: select HOUR(appointment_time) as hour, COUNT(*) as count from `appointments` where `appointment_date` between 2025-10-01 and 2025-10-31 and `status` = completed group by `hour` order by `hour` asc)
[ ] https://book.vai.me/admin/reports/custom - Illuminate\Database\QueryException SQLSTATE[42S22]: Column not found: 1054 Unknown column 'total_amount' in 'field list' (Connection: mysql, SQL: select sum(`total_amount`) as aggregate from `appointments` where `appointment_date` between 2025-07-02 and 2025-10-02)
[ ] https://book.vai.me/admin/reports/predictive-analytics - DivisionByZeroError Division by zero, line 220                $factors[$i] = $monthAverage / $overallAverage;
[ ] we are missing page managment in admin, how can admins edit pages for the frontend?
[ ] make sure Stuff seciton is more comprehansive, alow to upload photo, add biography, experiance, etc... from admin setting we should be able to make staff page public on FE, and if admin desires, make "Staff" page public where customer can view who they are booking the appointment with.
[ ] we have to make sure we have address (ditailed) with map in setting, so that customer can view and make sure they are in close range with the Salon before booking, I don't want to book customer from other states or cities. (if they desier, city is still ok, but I need them to confirm they can come), makes sense?
  
