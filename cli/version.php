#!/usr/bin/env php
<?php
/**
 * Version correction.
 *
 * composer.json 버전이 기준이 됩니다.
 * 추출된 기준 버전에 맞춰, 아래 파일들의 버전을 교정합니다.
 *
 * - axis4.php
 * - style.css
 * - package.json
 *
 * axis4.php 파일 안에 플러그인 헤더 버전을 변경.
 * axis4.php 파일 안에 상수로 선언한 버전을 변경. 상수 이름은 '{PREFIX}_VERSION' 처럼 지어야 인식됨.
 * style.css 파일 안에 선언된 헤더 버전을 변경.
 *
 * 1. 없는 파일은 생성하지 않습니다.
 * 2. 파일 안에 버전이 명시되지 않으면 새로 만들지 않습니다.
 */

if ('cli' !== php_sapi_name()) {
    exit;
}

define('ROOT_DIR', dirname(__DIR__));

/**
 * 파일의 내용을 읽음.
 *
 * @param string $file_name
 *
 * @return string 파일 내용. 파일이 없거나 읽을 수 없으면 빈 문자열을 리턴.
 */
function get_content(string $file_name): string
{
    if (file_exists($file_name) && is_readable($file_name)) {
        return file_get_contents($file_name);
    } else {
        return '';
    }
}

/**
 * 주석에서 버전 문자열을 추출.
 *
 * @param string $content 내용.
 *
 * @return array
 */
function detect_comment(string $content): array
{
    if ( ! $content) {
        return ['', 0, 0];
    }

    $m = preg_match(';/\*(.+?)\*/;ms', $content, $matches, PREG_OFFSET_CAPTURE);
    if ( ! $m) {
        return ['', 0, 0];
    }

    $headers = $matches[1][0];
    $offset  = $matches[1][1];
    $len     = 0;
    $version = '';

    if (preg_match('/version\s*:\s*(.+)/i', $headers, $matches, PREG_OFFSET_CAPTURE)) {
        $version = $matches[1][0];
        $offset  += $matches[1][1];
        $len     = strlen($version);
    }

    return [$version, $offset, $len];
}

/**
 * define() 선언에서 버전 문자열을 추출.
 *
 * {PREFIX}_VERSION 스타일로 선언해야 인식된다.
 *
 * @param string $content
 *
 * @return array
 */
function detect_define(string $content): array
{
    if ( ! $content) {
        return ['', 0, 0];
    }

    $version = '';
    $offset  = 0;
    $len     = 0;

    if (preg_match(
        '/define\s*\(\s*[\'\"][A-Za-z0-9]+_VERSION[\'\"]\s*,\s*[\'\"](.+)[\'\"]\s*\)\s*;/',
        $content,
        $matches,
        PREG_OFFSET_CAPTURE
    )) {
        $version = $matches[1][0];
        $offset  = $matches[1][1];
        $len     = strlen($version);
    }

    return [$version, $offset, $len];
}

/**
 * JSON 파일 내부의 버전 문자열을 추출.
 *
 * @param string $content
 *
 * @return array
 */
function detect_json(string $content): array
{
    if ( ! $content) {
        return ['', 0, 0];
    }

    $version = '';
    $offset  = 0;
    $len     = 0;

    if (preg_match('/"version"\s*:\s*"(.+)"/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $version = $matches[1][0];
        $offset  = $matches[1][1];
        $len     = strlen($version);
    }

    return [$version, $offset, $len];
}

/**
 * 버전을 변경 처리.
 *
 * @param string $content
 * @param string $version
 * @param array  $match
 *
 * @return string
 */
function replace_version(string $content, string $version, array $match): string
{
    if (is_array($match) && 3 === count($match) && is_int($match[1]) && is_int($match[2])) {
        if (
            strlen($content) > $match[1] + $match[2] &&
            $match[0] &&
            $match[0] === substr($content, $match[1], $match[2])
        ) {
            $before  = substr($content, 0, $match[1]);
            $after   = substr($content, $match[1] + $match[2]);
            $content = $before . $version . $after;
        }
    }

    return $content;
}

/**
 * composer.json 버전을 기준으로 다른 파일의 버전 문자열을 변경 처리한다.
 */
function correct_version()
{
    $files = [
        'composer.json' => ROOT_DIR . '/composer.json',
        'main.php'      => ROOT_DIR . '/axis4.php',
        'style.css'     => ROOT_DIR . '/style.css',
        'package.json'  => ROOT_DIR . '/package.json',
    ];

    // composer.json versioni extraction.
    $composer = get_content($files['composer.json']);
    if ($composer) {
        $cv = detect_json($composer);
        if ( ! $cv[0]) {
            die('No version found in composer.json.');
        }

        $functions = get_content($files['main.php']);
        if ($functions) {
            // header version correction
            $he = detect_comment($functions);
            if ($he[0] !== $cv[0]) {
                $functions = replace_version($functions, $cv[0], $he);
                if ($functions) {
                    file_put_contents($files['main.php'], $functions);
                }
            }

            // define version correction.
            $de = detect_define($functions);
            if ($de[0] !== $cv[0]) {
                $functions = replace_version($functions, $cv[0], $de);
                if ($functions) {
                    file_put_contents($files['main.php'], $functions);
                }
            }
        }

        // style.css correction (theme)
        $style = get_content($files['style.css']);
        if ($style) {
            $sv = detect_comment($style);
            if ($sv[0] !== $cv[0]) {
                $style = replace_version($style, $cv[0], $sv);
                if ($functions) {
                    file_put_contents($files['style.css'], $style);
                }
            }
        }

        // package.json correction
        $package = get_content($files['package.json']);
        if ($package) {
            $pv = detect_json($package);
            if ($pv[0] !== $cv[0]) {
                $package = replace_version($package, $cv[0], $pv);
                if ($package) {
                    file_put_contents($files['package.json'], $package);
                }
            }
        }
    }
}

correct_version();
