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
    $jsd       = array( );
    $jsd[ ]    = "var ddd = new Date();";
    $jsd[ ]    = "var ddz = new Date(ddd.getFullYear(), ddd.getMonth(), ddd.getDate(), ddd.getHours(), ddd.getMinutes(), 0,0);";
    $mkeya     = md5( 'all' );
    $big_table = array( );
    echo '<div class="row">';
    echo "\n" . '<div class="large-12 columns" id="' . $mkeya . '_l" style="width: 1000px; height: 150px;">"' . $mkeya . '_l"</div>';
    $jsd[ ] = "chartl['a181a603769c1f98ad927e7367c7aa51'] = new google.visualization.LineChart(document.getElementById('a181a603769c1f98ad927e7367c7aa51_l'));  ";
    $all_s  = " dataa['a181a603769c1f98ad927e7367c7aa51'] = [ddz";
    $jsd[ ] = " datal['a181a603769c1f98ad927e7367c7aa51'] = new google.visualization.DataTable(); ";
    $jsd[ ] = " datal['a181a603769c1f98ad927e7367c7aa51'].addColumn('datetime', 'Day'); ";
    echo '</div>'; // ends gragphs
    $all_domains = array( );
    $doamin_warn = array( );
    foreach ( $servers as $sk => $sv ) {
        $all_s .= ",0";
        $whmusername = $sv[ 0 ];
        $hash        = $sv[ 2 ];
        $jsd[ ]      = " datal['" . $mkeya . "'].addColumn('number', '" . $sk . "');";
        $mkey        = md5( $sk . ":" . $sv[ 1 ] );
        $query       = "https://" . $sv[ 1 ] . ":2087/json-api/listaccts?api.version=2";
        if ( $testing == false ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            $header[ 0 ] = "Authorization: WHM $whmusername:" . preg_replace( "'(\r|\n)'", "", $hash );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
            curl_setopt( $curl, CURLOPT_URL, $query );
            $result = curl_exec( $curl );
            if ( $result == false ) {
                die( "curl_exec threw error \"" . curl_error( $curl ) . "\" for $query" );
            } else {
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
                        $big_table[ ]             = array(
                             $sk,
                            $vv[ 'ip' ],
                            $vv[ 'user' ],
                            $vv[ 'domain' ] 
                        );
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
                    if ( isset( $all_domains[ $dm ] ) ) {
                        $doamin_warn[ ] = $dm . " is on '" . $all_domains[ $dm ] . "' AND '" . $sk . "'";
                    }
                    $all_domains[ $dm ] = $sk;
                }
            }
            //
            $list_d[ ]   = '<br><button class="alert button" id="' . $mkey . 'd_btnh" type="button" onclick="swap_sh(\'' . $mkey . '\',\'d\',0)">Hide Domains</button>';
            $domains_str = '<div style="display:none;"     id="' . $mkey . '_d">Domains :' . implode( ",\n ", $list_d ) . "</div>";
            $ud_btns .= ' <button class="success button" id="' . $mkey . 'd_btns" type="button" onclick="swap_sh(\'' . $mkey . '\',\'d\',1)">Show Domains</button>';
            //
            $list_u[ ] = '<br><button class="alert button"   id="' . $mkey . 'u_btnh" type="button" onclick="swap_sh(\'' . $mkey . '\',\'u\',0)">Hide users</button>';
            $users_str = '<div  style="display:none;"     id="' . $mkey . '_u">Users :' . implode( ",\n ", $list_u ) . "</div>";
            $ud_btns .= ' <button class="success button"  id="' . $mkey . 'u_btns" type="button" onclick="swap_sh(\'' . $mkey . '\',\'u\',1)">Show users</button>';
        } else {
            $domains_str = '';
            $users_str   = '';
            $ud_btns     = '';
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
    $all_s .= "]";
    $jsd[ ] = $all_s . ";";
    $jsd[ ] = "chartl['a181a603769c1f98ad927e7367c7aa51'].draw(datal['a181a603769c1f98ad927e7367c7aa51'], optionsl);";
    if ( count( $doamin_warn ) > 0 ) {
        echo '<div class="row">';
        echo '<div class="large-2 columns" ><h2>Warning</h2></div>';
        echo '<div class="large-10 columns" >' . implode( "\n<br>\n", $doamin_warn ) . '</div>';
        echo '</div>';
    }
    echo "<script type=\"text/javascript\">";
    if ( $testing ) {
        echo "var testing = true;";
    } else {
        echo "var testing = false;";
    }
    echo "</script>";
?>
<div id="big_tab">big_tab</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript">
google.charts.load('current', {
  'packages': ['gauge', 'corechart','table']
});
google.charts.setOnLoadCallback(startChart);

function startChart() {
<?php
    echo "\n" . implode( "\n", $jsd );
?>
  get_laod();
  go_big_tab();
}
function go_big_tab() {
  var big_tab = new google.visualization.DataTable();
  // Declare columns
  big_tab.addColumn('string', 'Server');
  big_tab.addColumn('string', 'IP');
  big_tab.addColumn('string', 'Username');
  big_tab.addColumn('string', 'Domain');
<?php
    echo "\n big_tab.addRows(" . json_encode( $big_table, true ) . ");\n";
?>
  var table = new google.visualization.Table(document.getElementById('big_tab'));
  table.draw(big_tab, {
    showRowNumber: true,
    width: '100%',
    height: '100%'
  });
}
</script>
<script src="js/vendor/jquery.min.js"></script>
    <script src="js/vendor/what-input.min.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>