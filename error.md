
[2026-01-12 01:20:31] local.DEBUG: Enterprise services initialized {"notification_service":false,"analytics_service":false,"audit_service":false}
[2026-01-12 01:20:31] local.ERROR: syntax error, unexpected token ">" {"view":{"view":"/var/www/html/resources/views/admin/vehicles/enterprise-edit.blade.php","data":{"errors":"<pre class=sf-dump id=sf-dump-692732469 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Support\\ViewErrorBag</span> {<a class=sf-dump-ref>#1935</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">bags</span>: []
</samp>}
</pre><script>Sfdump(\"sf-dump-692732469\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","vehicle":"<pre class=sf-dump id=sf-dump-1228290159 data-indent-pad=\"  \"><span class=sf-dump-note>App\\Models\\Vehicle</span> {<a class=sf-dump-ref>#2379</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
  #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"8 characters\">vehicles</span>\"
  #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
  #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
  +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
  #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
  +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
  #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
  +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
  +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
  #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:37</span> [<samp data-depth=2 class=sf-dump-compact>
    \"<span class=sf-dump-key>id</span>\" => <span class=sf-dump-num>70</span>
    \"<span class=sf-dump-key>registration_plate</span>\" => \"<span class=sf-dump-str title=\"14 characters\">0076487-219-16</span>\"
    \"<span class=sf-dump-key>vin</span>\" => \"<span class=sf-dump-str title=\"17 characters\">1HGCM8287TA123456</span>\"
    \"<span class=sf-dump-key>brand</span>\" => \"<span class=sf-dump-str title=\"6 characters\">Toyota</span>\"
    \"<span class=sf-dump-key>model</span>\" => \"<span class=sf-dump-str title=\"7 characters\">Corolla</span>\"
    \"<span class=sf-dump-key>color</span>\" => \"<span class=sf-dump-str title=\"5 characters\">Blanc</span>\"
    \"<span class=sf-dump-key>vehicle_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>fuel_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>transmission_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>status_id</span>\" => <span class=sf-dump-num>8</span>
    \"<span class=sf-dump-key>manufacturing_year</span>\" => <span class=sf-dump-num>2020</span>
    \"<span class=sf-dump-key>acquisition_date</span>\" => \"<span class=sf-dump-str title=\"10 characters\">2020-01-15</span>\"
    \"<span class=sf-dump-key>purchase_price</span>\" => \"<span class=sf-dump-str title=\"8 characters\">25000.00</span>\"
    \"<span class=sf-dump-key>current_value</span>\" => \"<span class=sf-dump-str title=\"8 characters\">20000.00</span>\"
    \"<span class=sf-dump-key>initial_mileage</span>\" => <span class=sf-dump-num>5000</span>
    \"<span class=sf-dump-key>current_mileage</span>\" => <span class=sf-dump-num>15200</span>
    \"<span class=sf-dump-key>engine_displacement_cc</span>\" => <span class=sf-dump-num>1600</span>
    \"<span class=sf-dump-key>power_hp</span>\" => <span class=sf-dump-num>120</span>
    \"<span class=sf-dump-key>seats</span>\" => <span class=sf-dump-num>5</span>
    \"<span class=sf-dump-key>status_reason</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>notes</span>\" => \"<span class=sf-dump-str title=\"26 characters\">V&#233;hicule en excellent &#233;tat</span>\"
    \"<span class=sf-dump-key>created_at</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-19 17:02:28</span>\"
    \"<span class=sf-dump-key>updated_at</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-21 21:27:34</span>\"
    \"<span class=sf-dump-key>deleted_at</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>organization_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>vehicle_name</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>category_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>depot_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>is_archived</span>\" => <span class=sf-dump-const>false</span>
    \"<span class=sf-dump-key>status</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>status_metadata</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>status_changed_at</span>\" => \"<span class=sf-dump-str title=\"26 characters\">2025-12-19 16:02:28.541965</span>\"
    \"<span class=sf-dump-key>status_changed_by</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>is_available</span>\" => <span class=sf-dump-const>true</span>
    \"<span class=sf-dump-key>current_driver_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>assignment_status</span>\" => \"<span class=sf-dump-str title=\"9 characters\">available</span>\"
    \"<span class=sf-dump-key>last_assignment_end</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-21 21:27:34</span>\"
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:37</span> [<samp data-depth=2 class=sf-dump-compact>
    \"<span class=sf-dump-key>id</span>\" => <span class=sf-dump-num>70</span>
    \"<span class=sf-dump-key>registration_plate</span>\" => \"<span class=sf-dump-str title=\"14 characters\">0076487-219-16</span>\"
    \"<span class=sf-dump-key>vin</span>\" => \"<span class=sf-dump-str title=\"17 characters\">1HGCM8287TA123456</span>\"
    \"<span class=sf-dump-key>brand</span>\" => \"<span class=sf-dump-str title=\"6 characters\">Toyota</span>\"
    \"<span class=sf-dump-key>model</span>\" => \"<span class=sf-dump-str title=\"7 characters\">Corolla</span>\"
    \"<span class=sf-dump-key>color</span>\" => \"<span class=sf-dump-str title=\"5 characters\">Blanc</span>\"
    \"<span class=sf-dump-key>vehicle_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>fuel_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>transmission_type_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>status_id</span>\" => <span class=sf-dump-num>8</span>
    \"<span class=sf-dump-key>manufacturing_year</span>\" => <span class=sf-dump-num>2020</span>
    \"<span class=sf-dump-key>acquisition_date</span>\" => \"<span class=sf-dump-str title=\"10 characters\">2020-01-15</span>\"
    \"<span class=sf-dump-key>purchase_price</span>\" => \"<span class=sf-dump-str title=\"8 characters\">25000.00</span>\"
    \"<span class=sf-dump-key>current_value</span>\" => \"<span class=sf-dump-str title=\"8 characters\">20000.00</span>\"
    \"<span class=sf-dump-key>initial_mileage</span>\" => <span class=sf-dump-num>5000</span>
    \"<span class=sf-dump-key>current_mileage</span>\" => <span class=sf-dump-num>15200</span>
    \"<span class=sf-dump-key>engine_displacement_cc</span>\" => <span class=sf-dump-num>1600</span>
    \"<span class=sf-dump-key>power_hp</span>\" => <span class=sf-dump-num>120</span>
    \"<span class=sf-dump-key>seats</span>\" => <span class=sf-dump-num>5</span>
    \"<span class=sf-dump-key>status_reason</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>notes</span>\" => \"<span class=sf-dump-str title=\"26 characters\">V&#233;hicule en excellent &#233;tat</span>\"
    \"<span class=sf-dump-key>created_at</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-19 17:02:28</span>\"
    \"<span class=sf-dump-key>updated_at</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-21 21:27:34</span>\"
    \"<span class=sf-dump-key>deleted_at</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>organization_id</span>\" => <span class=sf-dump-num>1</span>
    \"<span class=sf-dump-key>vehicle_name</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>category_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>depot_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>is_archived</span>\" => <span class=sf-dump-const>false</span>
    \"<span class=sf-dump-key>status</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>status_metadata</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>status_changed_at</span>\" => \"<span class=sf-dump-str title=\"26 characters\">2025-12-19 16:02:28.541965</span>\"
    \"<span class=sf-dump-key>status_changed_by</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>is_available</span>\" => <span class=sf-dump-const>true</span>
    \"<span class=sf-dump-key>current_driver_id</span>\" => <span class=sf-dump-const>null</span>
    \"<span class=sf-dump-key>assignment_status</span>\" => \"<span class=sf-dump-str title=\"9 characters\">available</span>\"
    \"<span class=sf-dump-key>last_assignment_end</span>\" => \"<span class=sf-dump-str title=\"19 characters\">2025-12-21 21:27:34</span>\"
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:11</span> [<samp data-depth=2 class=sf-dump-compact>
    \"<span class=sf-dump-key>acquisition_date</span>\" => \"<span class=sf-dump-str title=\"4 characters\">date</span>\"
    \"<span class=sf-dump-key>current_mileage</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>initial_mileage</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>manufacturing_year</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>purchase_price</span>\" => \"<span class=sf-dump-str title=\"9 characters\">decimal:2</span>\"
    \"<span class=sf-dump-key>current_value</span>\" => \"<span class=sf-dump-str title=\"9 characters\">decimal:2</span>\"
    \"<span class=sf-dump-key>engine_displacement_cc</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>power_hp</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>seats</span>\" => \"<span class=sf-dump-str title=\"7 characters\">integer</span>\"
    \"<span class=sf-dump-key>is_archived</span>\" => \"<span class=sf-dump-str title=\"7 characters\">boolean</span>\"
    \"<span class=sf-dump-key>deleted_at</span>\" => \"<span class=sf-dump-str title=\"8 characters\">datetime</span>\"
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
  #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
  #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
  +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
  +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
  #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:25</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => \"<span class=sf-dump-str title=\"18 characters\">registration_plate</span>\"
    <span class=sf-dump-index>1</span> => \"<span class=sf-dump-str title=\"3 characters\">vin</span>\"
    <span class=sf-dump-index>2</span> => \"<span class=sf-dump-str title=\"5 characters\">brand</span>\"
    <span class=sf-dump-index>3</span> => \"<span class=sf-dump-str title=\"5 characters\">model</span>\"
    <span class=sf-dump-index>4</span> => \"<span class=sf-dump-str title=\"5 characters\">color</span>\"
    <span class=sf-dump-index>5</span> => \"<span class=sf-dump-str title=\"15 characters\">vehicle_type_id</span>\"
    <span class=sf-dump-index>6</span> => \"<span class=sf-dump-str title=\"12 characters\">fuel_type_id</span>\"
    <span class=sf-dump-index>7</span> => \"<span class=sf-dump-str title=\"20 characters\">transmission_type_id</span>\"
    <span class=sf-dump-index>8</span> => \"<span class=sf-dump-str title=\"9 characters\">status_id</span>\"
    <span class=sf-dump-index>9</span> => \"<span class=sf-dump-str title=\"18 characters\">manufacturing_year</span>\"
    <span class=sf-dump-index>10</span> => \"<span class=sf-dump-str title=\"16 characters\">acquisition_date</span>\"
    <span class=sf-dump-index>11</span> => \"<span class=sf-dump-str title=\"14 characters\">purchase_price</span>\"
    <span class=sf-dump-index>12</span> => \"<span class=sf-dump-str title=\"13 characters\">current_value</span>\"
    <span class=sf-dump-index>13</span> => \"<span class=sf-dump-str title=\"15 characters\">initial_mileage</span>\"
    <span class=sf-dump-index>14</span> => \"<span class=sf-dump-str title=\"15 characters\">current_mileage</span>\"
    <span class=sf-dump-index>15</span> => \"<span class=sf-dump-str title=\"22 characters\">engine_displacement_cc</span>\"
    <span class=sf-dump-index>16</span> => \"<span class=sf-dump-str title=\"8 characters\">power_hp</span>\"
    <span class=sf-dump-index>17</span> => \"<span class=sf-dump-str title=\"5 characters\">seats</span>\"
    <span class=sf-dump-index>18</span> => \"<span class=sf-dump-str title=\"13 characters\">status_reason</span>\"
    <span class=sf-dump-index>19</span> => \"<span class=sf-dump-str title=\"5 characters\">notes</span>\"
    <span class=sf-dump-index>20</span> => \"<span class=sf-dump-str title=\"15 characters\">organization_id</span>\"
    <span class=sf-dump-index>21</span> => \"<span class=sf-dump-str title=\"12 characters\">vehicle_name</span>\"
    <span class=sf-dump-index>22</span> => \"<span class=sf-dump-str title=\"11 characters\">category_id</span>\"
    <span class=sf-dump-index>23</span> => \"<span class=sf-dump-str title=\"8 characters\">depot_id</span>\"
    <span class=sf-dump-index>24</span> => \"<span class=sf-dump-str title=\"11 characters\">is_archived</span>\"
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => \"<span class=sf-dump-str>*</span>\"
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-1228290159\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","vehicleTypes":"<pre class=sf-dump id=sf-dump-1480511114 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2361</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:11</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2627</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2628</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>2</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2629</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>3</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2630</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>4</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2631</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>5</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2632</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>6</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2633</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>7</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2634</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>8</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2635</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>9</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2636</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>10</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleType</span></span> {<a class=sf-dump-ref>#2637</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">vehicle_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:15</span> [ &#8230;15]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-1480511114\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","vehicleStatuses":"<pre class=sf-dump id=sf-dump-350179108 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2638</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:6</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2639</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2640</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>2</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2641</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>3</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2642</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>4</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2643</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>5</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleStatus
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleStatus</span></span> {<a class=sf-dump-ref>#2644</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"16 characters\">vehicle_statuses</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:14</span> [ &#8230;14]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:5</span> [ &#8230;5]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:11</span> [ &#8230;11]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-350179108\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","fuelTypes":"<pre class=sf-dump id=sf-dump-1168384918 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2645</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:4</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\FuelType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">FuelType</span></span> {<a class=sf-dump-ref>#2646</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"10 characters\">fuel_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\FuelType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">FuelType</span></span> {<a class=sf-dump-ref>#2647</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"10 characters\">fuel_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>2</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\FuelType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">FuelType</span></span> {<a class=sf-dump-ref>#2648</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"10 characters\">fuel_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>3</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\FuelType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">FuelType</span></span> {<a class=sf-dump-ref>#2649</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"10 characters\">fuel_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-1168384918\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","transmissionTypes":"<pre class=sf-dump id=sf-dump-473465093 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2650</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:2</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\TransmissionType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">TransmissionType</span></span> {<a class=sf-dump-ref>#2651</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"18 characters\">transmission_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\TransmissionType
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">TransmissionType</span></span> {<a class=sf-dump-ref>#2652</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"18 characters\">transmission_types</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>false</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-473465093\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","categories":"<pre class=sf-dump id=sf-dump-848136401 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2653</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: []
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-848136401\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","depots":"<pre class=sf-dump id=sf-dump-2087008268 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#2583</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:5</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleDepot
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleDepot</span></span> {<a class=sf-dump-ref>#2598</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"14 characters\">vehicle_depots</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:9</span> [ &#8230;9]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:17</span> [ &#8230;17]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleDepot
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleDepot</span></span> {<a class=sf-dump-ref>#2597</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"14 characters\">vehicle_depots</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:9</span> [ &#8230;9]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:17</span> [ &#8230;17]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>2</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleDepot
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleDepot</span></span> {<a class=sf-dump-ref>#2599</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"14 characters\">vehicle_depots</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:9</span> [ &#8230;9]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:17</span> [ &#8230;17]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>3</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleDepot
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleDepot</span></span> {<a class=sf-dump-ref>#2601</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"14 characters\">vehicle_depots</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:9</span> [ &#8230;9]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:17</span> [ &#8230;17]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>4</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\VehicleDepot
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">VehicleDepot</span></span> {<a class=sf-dump-ref>#2600</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"14 characters\">vehicle_depots</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:49</span> [ &#8230;49]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:9</span> [ &#8230;9]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:17</span> [ &#8230;17]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-2087008268\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","organizations":"<pre class=sf-dump id=sf-dump-985027560 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Support\\Collection</span> {<a class=sf-dump-ref>#2654</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:1</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\Organization
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">Organization</span></span> {<a class=sf-dump-ref>#2655</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"13 characters\">organizations</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:102</span> [ &#8230;102]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:102</span> [ &#8230;102]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:3</span> [ &#8230;3]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:31</span> [ &#8230;31]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">dates</span>: <span class=sf-dump-note>array:3</span> [ &#8230;3]
      #<span class=sf-dump-protected title=\"Protected property\">slugOptions</span>: <span class=sf-dump-const title=\"Uninitialized property\">? Spatie\\Sluggable\\SlugOptions</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-985027560\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","users":"<pre class=sf-dump id=sf-dump-809197813 data-indent-pad=\"  \"><span class=sf-dump-note>Illuminate\\Database\\Eloquent\\Collection</span> {<a class=sf-dump-ref>#3103</a><samp data-depth=1 class=sf-dump-expanded>
  #<span class=sf-dump-protected title=\"Protected property\">items</span>: <span class=sf-dump-note>array:16</span> [<samp data-depth=2 class=sf-dump-compact>
    <span class=sf-dump-index>0</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3102</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>1</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3101</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>2</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3100</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>3</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3099</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>4</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3098</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>5</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3097</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>6</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3096</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>7</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3095</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>8</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3094</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>9</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3092</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>10</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3093</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>11</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3091</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>12</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3090</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>13</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3080</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>14</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3081</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
    <span class=sf-dump-index>15</span> => <span class=\"sf-dump-note sf-dump-ellipsization\" title=\"App\\Models\\User
\"><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">App\\Models</span><span class=\"sf-dump-ellipsis sf-dump-ellipsis-note\">\\</span><span class=\"sf-dump-ellipsis-tail\">User</span></span> {<a class=sf-dump-ref>#3079</a><samp data-depth=3 class=sf-dump-compact>
      #<span class=sf-dump-protected title=\"Protected property\">connection</span>: \"<span class=sf-dump-str title=\"5 characters\">pgsql</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">table</span>: \"<span class=sf-dump-str title=\"5 characters\">users</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">primaryKey</span>: \"<span class=sf-dump-str title=\"2 characters\">id</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">keyType</span>: \"<span class=sf-dump-str title=\"3 characters\">int</span>\"
      +<span class=sf-dump-public title=\"Public property\">incrementing</span>: <span class=sf-dump-const>true</span>
      #<span class=sf-dump-protected title=\"Protected property\">with</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">withCount</span>: []
      +<span class=sf-dump-public title=\"Public property\">preventsLazyLoading</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">perPage</span>: <span class=sf-dump-num>15</span>
      +<span class=sf-dump-public title=\"Public property\">exists</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">wasRecentlyCreated</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">attributes</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">original</span>: <span class=sf-dump-note>array:38</span> [ &#8230;38]
      #<span class=sf-dump-protected title=\"Protected property\">changes</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">previous</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">casts</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">classCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">attributeCastCache</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dateFormat</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">appends</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">dispatchesEvents</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">observables</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relations</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">touches</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadCallback</span>: <span class=sf-dump-const>null</span>
      #<span class=sf-dump-protected title=\"Protected property\">relationAutoloadContext</span>: <span class=sf-dump-const>null</span>
      +<span class=sf-dump-public title=\"Public property\">timestamps</span>: <span class=sf-dump-const>true</span>
      +<span class=sf-dump-public title=\"Public property\">usesUniqueIds</span>: <span class=sf-dump-const>false</span>
      #<span class=sf-dump-protected title=\"Protected property\">hidden</span>: <span class=sf-dump-note>array:2</span> [ &#8230;2]
      #<span class=sf-dump-protected title=\"Protected property\">visible</span>: []
      #<span class=sf-dump-protected title=\"Protected property\">fillable</span>: <span class=sf-dump-note>array:7</span> [ &#8230;7]
      #<span class=sf-dump-protected title=\"Protected property\">guarded</span>: <span class=sf-dump-note>array:1</span> [ &#8230;1]
      #<span class=sf-dump-protected title=\"Protected property\">authPasswordName</span>: \"<span class=sf-dump-str title=\"8 characters\">password</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">rememberTokenName</span>: \"<span class=sf-dump-str title=\"14 characters\">remember_token</span>\"
      #<span class=sf-dump-protected title=\"Protected property\">accessToken</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">roleClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">permissionClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardClass</span>: <span class=sf-dump-const>null</span>
      -<span class=sf-dump-private title=\"Private property defined in class:&#10;`App\\Models\\User`\">wildcardPermissionsIndex</span>: <span class=sf-dump-const title=\"Uninitialized property\">? array</span>
      #<span class=sf-dump-protected title=\"Protected property\">forceDeleting</span>: <span class=sf-dump-const>false</span>
    </samp>}
  </samp>]
  #<span class=sf-dump-protected title=\"Protected property\">escapeWhenCastingToString</span>: <span class=sf-dump-const>false</span>
</samp>}
</pre><script>Sfdump(\"sf-dump-809197813\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
","changeRecommendations":"<pre class=sf-dump id=sf-dump-1558772795 data-indent-pad=\"  \">[]
</pre><script>Sfdump(\"sf-dump-1558772795\", {\"maxDepth\":3,\"maxStringLength\":160})</script>
"}},"userId":4,"exception":"[object] (Spatie\\LaravelIgnition\\Exceptions\\ViewException(code: 0): syntax error, unexpected token \">\" at /var/www/html/resources/views/admin/vehicles/enterprise-edit.blade.php:824)
[stacktrace]
#0 /var/www/html/vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php(124): Illuminate\\Filesystem\\Filesystem::Illuminate\\Filesystem\\{closure}()
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php(57): Illuminate\\Filesystem\\Filesystem->getRequire('/var/www/html/s...', Array)
#2 /var/www/html/vendor/livewire/livewire/src/Mechanisms/ExtendBlade/ExtendedCompilerEngine.php(22): Illuminate\\View\\Engines\\PhpEngine->evaluatePath('/var/www/html/s...', Array)
#3 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php(76): Livewire\\Mechanisms\\ExtendBlade\\ExtendedCompilerEngine->evaluatePath('/var/www/html/s...', Array)
#4 /var/www/html/vendor/livewire/livewire/src/Mechanisms/ExtendBlade/ExtendedCompilerEngine.php(10): Illuminate\\View\\Engines\\CompilerEngine->get('/var/www/html/r...', Array)
#5 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(208): Livewire\\Mechanisms\\ExtendBlade\\ExtendedCompilerEngine->get('/var/www/html/r...', Array)
#6 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(191): Illuminate\\View\\View->getContents()
#7 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(160): Illuminate\\View\\View->renderContents()
#8 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Response.php(78): Illuminate\\View\\View->render()
#9 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Response.php(34): Illuminate\\Http\\Response->setContent(Object(Illuminate\\View\\View))
#10 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(939): Illuminate\\Http\\Response->__construct(Object(Illuminate\\View\\View), 200, Array)
#11 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(906): Illuminate\\Routing\\Router::toResponse(Object(Illuminate\\Http\\Request), Object(Illuminate\\View\\View))
#12 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(821): Illuminate\\Routing\\Router->prepareResponse(Object(Illuminate\\Http\\Request), Object(Illuminate\\View\\View))
#13 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))
#14 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authorize.php(59): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#15 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authorize->handle(Object(Illuminate\\Http\\Request), Object(Closure), 'update', 'vehicle')
#16 /var/www/html/app/Http/Middleware/EnterprisePermissionMiddleware.php(223): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#17 /var/www/html/app/Http/Middleware/EnterprisePermissionMiddleware.php(170): App\\Http\\Middleware\\EnterprisePermissionMiddleware->checkRoutePermissions(Object(Illuminate\\Http\\Request), Object(Closure), 'admin.vehicles....')
#18 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): App\\Http\\Middleware\\EnterprisePermissionMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#19 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/EnsureEmailIsVerified.php(41): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#20 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\EnsureEmailIsVerified->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#21 /var/www/html/app/Http/Middleware/SetTenantSession.php(35): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#22 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): App\\Http\\Middleware\\SetTenantSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#23 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#24 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#25 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php(63): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#26 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authenticate->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#27 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php(87): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#28 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#29 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#30 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\View\\Middleware\\ShareErrorsFromSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#31 /var/www/html/vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php(120): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#32 /var/www/html/vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php(63): Illuminate\\Session\\Middleware\\StartSession->handleStatefulRequest(Object(Illuminate\\Http\\Request), Object(Illuminate\\Session\\Store), Object(Closure))
#33 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Session\\Middleware\\StartSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#34 /var/www/html/vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php(36): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#35 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#36 /var/www/html/vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php(74): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#37 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Cookie\\Middleware\\EncryptCookies->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#38 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#39 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(821): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))
#40 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(800): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))
#41 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(764): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))
#42 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(753): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))
#43 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))
#44 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))
#45 /var/www/html/vendor/livewire/livewire/src/Features/SupportDisablingBackButtonCache/DisableBackButtonCacheMiddleware.php(19): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#46 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Livewire\\Features\\SupportDisablingBackButtonCache\\DisableBackButtonCacheMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#47 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#48 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#49 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#50 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#51 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php(51): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#52 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#53 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#54 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#55 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php(109): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#56 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#57 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#58 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#59 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php(58): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#60 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#61 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#62 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))
#63 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))
#64 /var/www/html/public/index.php(51): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))
#65 {main}

[previous exception] [object] (ParseError(code: 0): syntax error, unexpected token \">\" at /var/www/html/storage/framework/views/131823688ad8a3d1e1a4c5aee0434f3c.php:824)
[stacktrace]
#0 /var/www/html/vendor/laravel/framework/src/Illuminate/Filesystem/Filesystem.php(124): Illuminate\\Filesystem\\Filesystem::Illuminate\\Filesystem\\{closure}()
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Engines/PhpEngine.php(57): Illuminate\\Filesystem\\Filesystem->getRequire('/var/www/html/s...', Array)
#2 /var/www/html/vendor/livewire/livewire/src/Mechanisms/ExtendBlade/ExtendedCompilerEngine.php(22): Illuminate\\View\\Engines\\PhpEngine->evaluatePath('/var/www/html/s...', Array)
#3 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Engines/CompilerEngine.php(76): Livewire\\Mechanisms\\ExtendBlade\\ExtendedCompilerEngine->evaluatePath('/var/www/html/s...', Array)
#4 /var/www/html/vendor/livewire/livewire/src/Mechanisms/ExtendBlade/ExtendedCompilerEngine.php(10): Illuminate\\View\\Engines\\CompilerEngine->get('/var/www/html/r...', Array)
#5 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(208): Livewire\\Mechanisms\\ExtendBlade\\ExtendedCompilerEngine->get('/var/www/html/r...', Array)
#6 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(191): Illuminate\\View\\View->getContents()
#7 /var/www/html/vendor/laravel/framework/src/Illuminate/View/View.php(160): Illuminate\\View\\View->renderContents()
#8 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Response.php(78): Illuminate\\View\\View->render()
#9 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Response.php(34): Illuminate\\Http\\Response->setContent(Object(Illuminate\\View\\View))
#10 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(939): Illuminate\\Http\\Response->__construct(Object(Illuminate\\View\\View), 200, Array)
#11 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(906): Illuminate\\Routing\\Router::toResponse(Object(Illuminate\\Http\\Request), Object(Illuminate\\View\\View))
#12 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(821): Illuminate\\Routing\\Router->prepareResponse(Object(Illuminate\\Http\\Request), Object(Illuminate\\View\\View))
#13 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Routing\\Router->Illuminate\\Routing\\{closure}(Object(Illuminate\\Http\\Request))
#14 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authorize.php(59): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#15 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authorize->handle(Object(Illuminate\\Http\\Request), Object(Closure), 'update', 'vehicle')
#16 /var/www/html/app/Http/Middleware/EnterprisePermissionMiddleware.php(223): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#17 /var/www/html/app/Http/Middleware/EnterprisePermissionMiddleware.php(170): App\\Http\\Middleware\\EnterprisePermissionMiddleware->checkRoutePermissions(Object(Illuminate\\Http\\Request), Object(Closure), 'admin.vehicles....')
#18 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): App\\Http\\Middleware\\EnterprisePermissionMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#19 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/EnsureEmailIsVerified.php(41): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#20 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\EnsureEmailIsVerified->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#21 /var/www/html/app/Http/Middleware/SetTenantSession.php(35): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#22 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): App\\Http\\Middleware\\SetTenantSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#23 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php(50): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#24 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Routing\\Middleware\\SubstituteBindings->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#25 /var/www/html/vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php(63): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#26 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Auth\\Middleware\\Authenticate->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#27 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php(87): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#28 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#29 /var/www/html/vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#30 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\View\\Middleware\\ShareErrorsFromSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#31 /var/www/html/vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php(120): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#32 /var/www/html/vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php(63): Illuminate\\Session\\Middleware\\StartSession->handleStatefulRequest(Object(Illuminate\\Http\\Request), Object(Illuminate\\Session\\Store), Object(Closure))
#33 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Session\\Middleware\\StartSession->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#34 /var/www/html/vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php(36): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#35 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Cookie\\Middleware\\AddQueuedCookiesToResponse->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#36 /var/www/html/vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php(74): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#37 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Cookie\\Middleware\\EncryptCookies->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#38 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#39 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(821): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))
#40 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(800): Illuminate\\Routing\\Router->runRouteWithinStack(Object(Illuminate\\Routing\\Route), Object(Illuminate\\Http\\Request))
#41 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(764): Illuminate\\Routing\\Router->runRoute(Object(Illuminate\\Http\\Request), Object(Illuminate\\Routing\\Route))
#42 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(753): Illuminate\\Routing\\Router->dispatchToRoute(Object(Illuminate\\Http\\Request))
#43 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(200): Illuminate\\Routing\\Router->dispatch(Object(Illuminate\\Http\\Request))
#44 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Foundation\\Http\\Kernel->Illuminate\\Foundation\\Http\\{closure}(Object(Illuminate\\Http\\Request))
#45 /var/www/html/vendor/livewire/livewire/src/Features/SupportDisablingBackButtonCache/DisableBackButtonCacheMiddleware.php(19): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#46 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Livewire\\Features\\SupportDisablingBackButtonCache\\DisableBackButtonCacheMiddleware->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#47 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#48 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php(31): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#49 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#50 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TransformsRequest.php(21): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#51 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php(51): Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#52 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\TrimStrings->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#53 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php(27): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#54 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\ValidatePostSize->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#55 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php(109): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#56 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#57 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php(48): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#58 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\HandleCors->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#59 /var/www/html/vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php(58): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#60 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(219): Illuminate\\Http\\Middleware\\TrustProxies->handle(Object(Illuminate\\Http\\Request), Object(Closure))
#61 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Http\\Request))
#62 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(175): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))
#63 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(144): Illuminate\\Foundation\\Http\\Kernel->sendRequestThroughRouter(Object(Illuminate\\Http\\Request))
#64 /var/www/html/public/index.php(51): Illuminate\\Foundation\\Http\\Kernel->handle(Object(Illuminate\\Http\\Request))
#65 {main}
"}
