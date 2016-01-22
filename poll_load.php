<?
    include 'vars.php';
    $out = array( );
    foreach ( $servers as $sk => $sv ) {
        $whmusername = $sv[ 0 ];
        $hash        = $sv[ 2 ];
        //get load avg
        $query       = "https://" . $sv[ 1 ] . ":2087/json-api/loadavg";
        $curl        = curl_init();
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
        $ar                                 = json_decode( $result, true );
        $out[ md5( $sk . ":" . $sv[ 1 ] ) ] = $ar;
    }
    echo json_encode( $out );
?>