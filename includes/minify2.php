<?php
function wpfaster _init_minify_html() {
    if ( (int) get_option( 'lhf_minify_html_active' ) === 1 ) {
        ob_start( 'wpfaster _minify_html_output' );
    }
}

if ( ! is_admin() ) {
    if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        add_action( 'init', 'wpfaster _init_minify_html', 1 );
    }
}

function wpfaster _minify_html_output( $buffer ) {
    if ( substr( ltrim( $buffer ), 0, 5 ) === '<?xml' ) {
        return ( $buffer );
    }

    $mod = '/s';
    if ( (int) get_option( 'lhf_minify_html_utf8' ) === 1 && mb_detect_encoding( $buffer, 'UTF-8', true ) ) {
        $mod = '/u';
    }

    $buffer = str_replace(
        [
            chr( 13 ) . chr( 10 ),
            chr( 9 ),
        ],
        [
            chr( 10 ),
            '',
        ],
        $buffer
    );

    $buffer = str_ireplace(
        [
            '<script',
            '/script>',
            '<pre',
            '/pre>',
            '<textarea',
            '/textarea>',
            '<style',
            '/style>',
        ],
        [
            'M1N1FY-ST4RT<script',
            '/script>M1N1FY-3ND',
            'M1N1FY-ST4RT<pre',
            '/pre>M1N1FY-3ND',
            'M1N1FY-ST4RT<textarea',
            '/textarea>M1N1FY-3ND',
            'M1N1FY-ST4RT<style',
            '/style>M1N1FY-3ND',
        ],
        $buffer
    );

    $split       = explode( 'M1N1FY-3ND', $buffer );
    $split_count = count( $split );
    $buffer      = '';

    for ( $i = 0; $i < $split_count; $i++ ) {
        $ii = strpos( $split[ $i ], 'M1N1FY-ST4RT' );

        if ( $ii !== false ) {
            $process = substr( $split[ $i ], 0, $ii );
            $asis    = substr( $split[ $i ], $ii + 12 );

            if ( substr( $asis, 0, 7 ) === '<script' ) {
                $split2       = explode( chr( 10 ), $asis );
                $split2_count = count( $split2 );
                $asis         = '';

                for ( $iii = 0; $iii < $split2_count; $iii ++ ) {
                    if ( $split2[ $iii ] ) {
                        $asis .= trim( $split2[ $iii ] ) . chr( 10 );
                    }
                    if ( (int) get_option( 'lhf_minify_javascript' ) === 1 ) {
                        if ( strpos( $split2[ $iii ], '//' ) !== false && substr( trim( $split2[ $iii ] ), -1 ) === ';' ) {
                            $asis .= chr( 10 );
                        }
                    }
                }

                if ( $asis ) {
                    $asis = substr( $asis, 0, -1 );
                }
                if ( (int) get_option( 'lhf_minify_html_comments' ) === 1 ) {
                    $asis = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis );
                }
                if ( (int) get_option( 'lhf_minify_javascript' ) === 1 ) {
                    $asis = str_replace(
                        [
                            ';' . chr( 10 ),
                            '>' . chr( 10 ),
                            '{' . chr( 10 ),
                            '}' . chr( 10 ),
                            ',' . chr( 10 ),
                        ],
                        [
                            ';',
                            '>',
                            '{',
                            '}',
                            ',',
                        ],
                        $asis
                    );
                }
            } elseif ( substr( $asis, 0, 6 ) === '<style' ) {
                $asis = preg_replace(
                    [
                        '/\>[^\S ]+' . $mod,
                        '/[^\S ]+\<' . $mod,
                        '/(\s)+' . $mod,
                    ],
                    [
                        '>',
                        '<',
                        '\\1',
                    ],
                    $asis
                );

                if ( (int) get_option( 'lhf_minify_html_comments' ) === 1 ) {
                    $asis = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis );
                }

                $asis = str_replace(
                    [
                        chr( 10 ),
                        ' {',
                        '{ ',
                        ' }',
                        '} ',
                        '( ',
                        ' )',
                        ' :',
                        ': ',
                        ' ;',
                        '; ',
                        ' ,',
                        ', ',
                        ';}',
                    ],
                    [
                        '',
                        '{',
                        '{',
                        '}',
                        '}',
                        '(',
                        ')',
                        ':',
                        ':',
                        ';',
                        ';',
                        ',',
                        ',',
                        '}',
                    ],
                    $asis
                );
            }
        } else {
            $process = $split[ $i ];
            $asis    = '';
        }
        $process = preg_replace(
            [
                '/\>[^\S ]+' . $mod,
                '/[^\S ]+\<' . $mod,
                '/(\s)+' . $mod,
            ],
            [
                '>',
                '<',
                '\\1',
            ],
            $process
        );
        if ( (int) get_option( 'lhf_minify_html_comments' ) === 1 ) {
            $process = preg_replace( '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->' . $mod, '', $process );
        }
        $buffer .= $process . $asis;
    }

    $buffer = str_replace(
        [
            chr( 10 ) . '<script',
            chr( 10 ) . '<style',
            '*/' . chr( 10 ),
            'M1N1FY-ST4RT',
        ],
        [
            '<script',
            '<style',
            '*/',
            '',
        ],
        $buffer
    );

    if ( (int) get_option( 'lhf_minify_html_xhtml' ) === 1 && strtolower( substr( ltrim( $buffer ), 0, 15 ) ) === '<!doctype html>' ) {
        $buffer = str_replace( ' />', '>', $buffer );
    }

    return ( $buffer );
}
