<?
    session_start();
    include 'vars.php';
    $out = array( );
    foreach ( $servers as $sk => $sv ) {
        $whmusername = $sv[ 0 ];
        $hash        = $sv[ 2 ];
        if ( $testing ) {
            $ar                                 = array( );
            $ar[ 'one' ]                        = ( mt_rand( 0, 1000 ) / 1000 );
            $ar[ 'five' ]                       = ( $ar[ 'one' ] + ( mt_rand( 0, 1000 ) / 1000 ) ) / 2;
            $ar[ 'fifteen' ]                    = ( $ar[ 'one' ] + $ar[ 'one' ] + ( mt_rand( 0, 1000 ) / 1000 ) ) / 3;
            $out[ md5( $sk . ":" . $sv[ 1 ] ) ] = $ar;
        } else {
            //get load avg
            $query = "https://" . $sv[ 1 ] . ":2087/json-api/loadavg";
            $curl  = curl_init();
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 15 );
            $header[ 0 ] = "Authorization: WHM $whmusername:" . preg_replace( "'(\r|\n)'", "", $hash );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
            curl_setopt( $curl, CURLOPT_URL, $query );
            $result = curl_exec( $curl );
            if ( $result == false ) {
                error_log( "curl_exec threw error \"" . curl_error( $curl ) . "\" for $query" );
                $ar              = array( );
                $ar[ 'one' ]     = 0;
                $ar[ 'five' ]    = 10;
                $ar[ 'fifteen' ] = 15;
            } else {
                curl_close( $curl );
                $ar = json_decode( $result, true );
                if ( !isset( $ar[ 'one' ] ) ) {
                    // bad something    
                    $ar              = array( );
                    $ar[ 'one' ]     = 15;
                    $ar[ 'five' ]    = 10;
                    $ar[ 'fifteen' ] = 0;
                }
            }
            $out[ md5( $sk . ":" . $sv[ 1 ] ) ] = $ar;
        }
    }
    echo json_encode( $out );
?>