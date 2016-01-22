<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Servers</title>
    <link rel="stylesheet" href="css/foundation.css" />
    <link rel="stylesheet" href="css/app.css" />
  </head>
  <body>
      <div class="row">
      <div class="large-12 columns">
        <h1>Server Stats</h1>
      </div>
    </div>
<?
    if ( !is_file( 'vars.php' ) ) {
        die( 'Copy "vars.example.php" to "vars.php" and enter the right info' );
    }
    require 'vars.php';
    $jsd    = array( );
    $jsd[ ] = "var ddd = new Date();";
    $jsd[ ] = "var ddz = new Date(ddd.getFullYear(), ddd.getMonth(), ddd.getDate(), ddd.getHours(), ddd.getMinutes(), 0,0);";
    foreach ( $servers as $sk => $sv ) {
        $whmusername = $sv[ 0 ];
        $hash        = $sv[ 2 ];
        $mkey        = md5( $sk . ":" . $sv[ 1 ] );
        $query       = "https://" . $sv[ 1 ] . ":2087/json-api/listaccts?api.version=2";
        if ( 1 ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $header[ 0 ] = "Authorization: WHM $whmusername:" . preg_replace( "'(\r|\n)'", "", $hash );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
            curl_setopt( $curl, CURLOPT_URL, $query );
            $result = curl_exec( $curl );
            if ( $result == false ) {
                error_log( "curl_exec threw error \"" . curl_error( $curl ) . "\" for $query" );
            }
            curl_close( $curl );
            $ar      = json_decode( $result, true );
            $accs    = $ar[ 'data' ][ 'acct' ];
            $domains = array( );
            foreach ( $accs as $kk => $vv ) {
                if ( $vv[ 'suspendtime' ] != 0 ) {
                    //echo "<br>" . $vv[ 'domain' ] . ' ' . date( 'r', $vv[ 'suspendtime' ] );
                } else {
                    //echo "<pre>".print_r($vv,1)."</pre>";
                    $domains[ $vv[ 'user' ] ] = $vv[ 'domain' ];
                }
            }
            ksort( $domains );
            $list_d      = array( );
            $list_u      = array( );
            $domains_str = '';
            $users_str   = '';
            $ud_btns     = '';
            foreach ( $domains as $cn => $dm ) {
                $list_d[ ] = '<a href="http://' . $dm . '">' . $dm . '</a>';
                $list_u[ ] = $cn;
            }
            //
            $list_d[ ]   = '<br><button class="alert button" id="' . $mkey . 'd_btnh" type="button" onclick="swap_sh(\'' . $mkey . '\',\'d\',0)">Hide Domains</button>';
            $domains_str = '<div style="display:none;"     id="' . $mkey . '_d">Domains :' . implode( ",\n ", $list_d ) . "</div>";
            $ud_btns .= ' <button class="success button" id="' . $mkey . 'd_btns" type="button" onclick="swap_sh(\'' . $mkey . '\',\'d\',1)">Show Domains</button>';
            //
            $list_u[ ] = '<br><button class="alert button"   id="' . $mkey . 'u_btnh" type="button" onclick="swap_sh(\'' . $mkey . '\',\'u\',0)">Hide users</button>';
            $users_str = '<div  style="display:none;"     id="' . $mkey . '_u">Users :' . implode( ",\n ", $list_u ) . "</div>";
            $ud_btns .= ' <button class="success button"  id="' . $mkey . 'u_btns" type="button" onclick="swap_sh(\'' . $mkey . '\',\'u\',1)">Show users</button>';
        }
        // echo " holds <pre>" . print_r( $domains, 1 ) . "</pre>";
        echo '<div class="row">
      <div class="large-12 columns">
        <h3>' . $sk . '</h3>
      </div>';
        echo '<div class="row">';
        // echo "\n" . '<div class="large-2 columns" id="' . $mkey . '_h">"' . $mkey . '_h"</div>';
        echo "\n" . '<div class="large-5 columns" id="' . $mkey . '_g" style="width: 360px; height: 120px;">"' . $mkey . '_g"</div>';
        echo "\n" . '<div class="large-5 columns" id="' . $mkey . '_l" style="width: 800px; height: 150px;">"' . $mkey . '_l"</div>';
        $jsd[ ] = " dataa['" . $mkey . "'] = [0,0,0];";
        $jsd[ ] = " datal['" . $mkey . "'] = new google.visualization.DataTable();";
        $jsd[ ] = " datal['" . $mkey . "'].addColumn('datetime', 'Day');";
        $jsd[ ] = " datal['" . $mkey . "'].addColumn('number', '5');";
        $jsd[ ] = " datal['" . $mkey . "'].addColumn('number', '10');";
        $jsd[ ] = " datal['" . $mkey . "'].addColumn('number', '15');";
        //  $jsd[ ] = " datal['" . $mkey . "'].addRow([ddz,0,0,0]);";
        //console.log(ddz);
        echo '</div>'; // ends gragphs
        echo '<div class="row"><div class="large-12 columns">' . $ud_btns . '</div></div>';
        echo '<div class="row"><div class="large-12 columns">' . $domains_str . '</div></div>';
        echo '<div class="row"><div class="large-12 columns">' . $users_str . '</div></div>';
        echo '</div>' . "\n\n\n\n"; // ends per server
    }
    echo "<script type=\"text/javascript\">

</script>";
    //<script src='http://code.jquery.com/jquery-1.12.0.min.js'></script>
?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/main.min.js"></script>
<script type="text/javascript">
google.charts.load('current', {
  'packages': ['gauge', 'corechart']
});
google.charts.setOnLoadCallback(startChart);

function startChart() {
<?php
    echo "\n" . implode( "\n", $jsd );
?>
  get_laod();
}


</script>

<script src="js/vendor/jquery.min.js"></script>
    <script src="js/vendor/what-input.min.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>