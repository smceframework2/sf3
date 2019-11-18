<?php

namespace SF3\Core;

use Illuminate\Support\Pluralizer;

class Str
{
	protected static $snakeCache     = [];
	protected static $camelCache     = [];
	protected static $camelCaseCache = [];
	protected static $studlyCache    = [];

	public static function camelcase($value): string
	{
		if (isset(static::$camelCaseCache[$value])) {
			return static::$camelCaseCache[$value];
		}

		return static::$camelCaseCache[$value] = ucfirst(self::lower($value));
	}

	public static function CleanPhoneNumber($phone, $zero = false)
	{
		$phone = str_replace(['(', ')', ' '], '', $phone);

		return (!$zero && substr($phone, 0, 1) ? substr($phone, 1) : $phone);
	}

	public static function randomNumbers($max = 999999, $min = 100000): string
	{
		return rand($min, $max);
	}

	public static function ascii($value)
	{
		foreach (static::charsArray() as $key => $val) {
			$value = str_replace($val, $key, $value);
		}

		return preg_replace('/[^\\x20-\\x7E]/u', '', $value);
	}

	public static function camel($value)
	{
		if (isset(static::$camelCache[$value])) {
			return static::$camelCache[$value];
		}

		return static::$camelCache[$value] = lcfirst(static::studly($value));
	}

	public static function contains($haystack, $needles)
	{
		foreach ((array) $needles as $needle) {
			if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
				return true;
			}
		}

		return false;
	}

	public static function endsWith($haystack, $needles)
	{
		foreach ((array) $needles as $needle) {
			if ((string) $needle === static::substr($haystack, -static::length($needle))) {
				return true;
			}
		}

		return false;
	}

	public static function finish($value, $cap)
	{
		$quoted = preg_quote($cap, '/');

		return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
	}

	public static function is($pattern, $value)
	{
		if ($pattern == $value) {
			return true;
		}
		$pattern = preg_quote($pattern, '#');
		$pattern = str_replace('\\*', '.*', $pattern);

		return (bool) preg_match('#^' . $pattern . '\\z#u', $value);
	}

	public static function length($value)
	{
		return mb_strlen($value);
	}

	public static function limit($value, $limit = 100, $end = '...')
	{
		if (mb_strwidth($value, 'UTF-8') <= $limit) {
			return $value;
		}

		return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
	}

	public static function lower($value)
	{
		return mb_strtolower($value, 'UTF-8');
	}

	public static function words($value, $words = 100, $end = '...')
	{
		preg_match('/^\\s*+(?:\\S++\\s*+){1,' . $words . '}/u', $value, $matches);
		if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
			return $value;
		}

		return rtrim($matches[0]) . $end;
	}

	public static function parseCallback($callback, $default)
	{
		return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
	}

	public static function plural($value, $count = 2)
	{
		return Pluralizer::plural($value, $count);
	}

	public static function random($length = 16)
	{
		$string = '';
		while (($len = static::length($string)) < $length) {
			$size   = $length - $len;
			$bytes  = random_bytes($size);
			$string .= static::substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
		}

		return $string;
	}

	public static function randomBytes($length = 16)
	{
		return random_bytes($length);
	}

	public static function quickRandom($length = 16)
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return static::substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
	}

	public static function equals($knownString, $userInput)
	{
		return hash_equals($knownString, $userInput);
	}

	public static function replaceFirst($search, $replace, $subject)
	{
		$position = strpos($subject, $search);
		if ($position !== false) {
			return substr_replace($subject, $replace, $position, strlen($search));
		}

		return $subject;
	}

	public static function replaceLast($search, $replace, $subject)
	{
		$position = strrpos($subject, $search);
		if ($position !== false) {
			return substr_replace($subject, $replace, $position, strlen($search));
		}

		return $subject;
	}

	public static function upper($value)
	{
		return mb_strtoupper($value, 'UTF-8');
	}

	public static function title($value)
	{
		return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
	}

	public static function singular($value)
	{
		return Pluralizer::singular($value);
	}

	public static function slug($title, $separator = '-')
	{
		$tr = ['ş'  => 's',
		       'ç'  => 'c',
		       'ğ'  => 'g',
		       'ö'  => 'o',
		       'ü'  => 'u',
		       'â'  => 'a',
		       'û'  => 'u',
		       'ô'  => 'o',
		       'î'  => 'i',
		       'ı'  => 'i',
		       'ä'  => 'a',
		       'İ'  => 'i',
		       'Ğ'  => 'g',
		       'Ç'  => 'c',
		       'Ş'  => 's',
		       'Ö'  => 'o',
		       'Ü'  => 'u',
		       'ÅŸ' => 's',
		       'ÄŸ' => 'g',
		       'Ä±' => 'i',
		       'Å?' => 's',
		       'Ä°' => 'i',
		       'Ä?' => 'g'];
		$title = str_replace(array_keys($tr), array_values($tr),$title);
		$title = static::ascii($title);
		$flip  = $separator == '-' ? '_' : '-';
		$title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
		$title = preg_replace('![^' . preg_quote($separator) . '\\pL\\pN\\s]+!u', '', mb_strtolower($title));
		$title = preg_replace('![' . preg_quote($separator) . '\\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	public static function snake($value, $delimiter = '_')
	{
		$key = $value;
		if (isset(static::$snakeCache[$key][$delimiter])) {
			return static::$snakeCache[$key][$delimiter];
		}
		if (!ctype_lower($value)) {
			$value = preg_replace('/\\s+/u', '', $value);
			$value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
		}

		return static::$snakeCache[$key][$delimiter] = $value;
	}

	public static function startsWith($haystack, $needles)
	{
		foreach ((array) $needles as $needle) {
			if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
				return true;
			}
		}

		return false;
	}

	public static function studly($value)
	{
		$key = $value;
		if (isset(static::$studlyCache[$key])) {
			return static::$studlyCache[$key];
		}
		$value = ucwords(str_replace(['-', '_'], ' ', $value));

		return static::$studlyCache[$key] = str_replace(' ', '', $value);
	}

	public static function substr($string, $start, $length = null)
	{
		return mb_substr($string, $start, $length, 'UTF-8');
	}

	public static function ucfirst($string)
	{
		return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
	}

	protected static function charsArray()
	{
		static $charsArray;
		if (isset($charsArray)) {
			return $charsArray;
		}

		return $charsArray = ['0' => ['°', '₀', '۰'], '1' => ['¹', '₁', '۱'], '2' => ['²', '₂', '۲'], '3' => ['³', '₃', '۳'], '4' => ['⁴', '₄', '۴', '٤'], '5' => ['⁵', '₅', '۵', '٥'], '6' => ['⁶', '₆', '۶', '٦'], '7' => ['⁷', '₇', '۷'], '8' => ['⁸', '₈', '۸'], '9' => ['⁹', '₉', '۹'], 'a' => ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ', 'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', 'အ', 'ာ', 'ါ', 'ǻ', 'ǎ', 'ª', 'ა', 'अ', 'ا'], 'b' => ['б', 'β', 'Ъ', 'Ь', 'ب', 'ဗ', 'ბ'], 'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ'], 'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ'], 'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э', 'є', 'ə', 'ဧ', 'ေ', 'ဲ', 'ე', 'ए', 'إ', 'ئ'], 'f' => ['ф', 'φ', 'ف', 'ƒ', 'ფ'], 'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ'], 'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ'], 'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი', 'इ'], 'j' => ['ĵ', 'ј', 'Ј', 'ჯ', 'ج'], 'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک'], 'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ'], 'm' => ['м', 'μ', 'م', 'မ', 'მ'], 'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ'], 'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ', 'ို', 'ǒ', 'ǿ', 'º', 'ო', 'ओ'], 'p' => ['п', 'π', 'ပ', 'პ', 'پ'], 'q' => ['ყ'], 'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ'], 's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს'], 't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ'], 'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ', 'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ'], 'v' => ['в', 'ვ', 'ϐ'], 'w' => ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ'], 'x' => ['χ', 'ξ'], 'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ'], 'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ'], 'aa' => ['ع', 'आ', 'آ'], 'ae' => ['ä', 'æ', 'ǽ'], 'ai' => ['ऐ'], 'at' => ['@'], 'ch' => ['ч', 'ჩ', 'ჭ', 'چ'], 'dj' => ['ђ', 'đ'], 'dz' => ['џ', 'ძ'], 'ei' => ['ऍ'], 'gh' => ['غ', 'ღ'], 'ii' => ['ई'], 'ij' => ['ĳ'], 'kh' => ['х', 'خ', 'ხ'], 'lj' => ['љ'], 'nj' => ['њ'], 'oe' => ['ö', 'œ', 'ؤ'], 'oi' => ['ऑ'], 'oii' => ['ऒ'], 'ps' => ['ψ'], 'sh' => ['ш', 'შ', 'ش'], 'shch' => ['щ'], 'ss' => ['ß'], 'sx' => ['ŝ'], 'th' => ['þ', 'ϑ', 'ث', 'ذ', 'ظ'], 'ts' => ['ц', 'ც', 'წ'], 'ue' => ['ü'], 'uu' => ['ऊ'], 'ya' => ['я'], 'yu' => ['ю'], 'zh' => ['ж', 'ჟ', 'ژ'], '(c)' => ['©'], 'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ'], 'B' => ['Б', 'Β', 'ब'], 'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'], 'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'], 'E' => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə'], 'F' => ['Ф', 'Φ'], 'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'], 'H' => ['Η', 'Ή', 'Ħ'], 'I' => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ', 'ϒ'], 'K' => ['К', 'Κ'], 'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल'], 'M' => ['М', 'Μ'], 'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'], 'O' => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ'], 'P' => ['П', 'Π'], 'R' => ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ'], 'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'], 'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'], 'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ'], 'V' => ['В'], 'W' => ['Ω', 'Ώ', 'Ŵ'], 'X' => ['Χ', 'Ξ'], 'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ'], 'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'], 'AE' => ['Ä', 'Æ', 'Ǽ'], 'CH' => ['Ч'], 'DJ' => ['Ђ'], 'DZ' => ['Џ'], 'GX' => ['Ĝ'], 'HX' => ['Ĥ'], 'IJ' => ['Ĳ'], 'JX' => ['Ĵ'], 'KH' => ['Х'], 'LJ' => ['Љ'], 'NJ' => ['Њ'], 'OE' => ['Ö', 'Œ'], 'PS' => ['Ψ'], 'SH' => ['Ш'], 'SHCH' => ['Щ'], 'SS' => ['ẞ'], 'TH' => ['Þ'], 'TS' => ['Ц'], 'UE' => ['Ü'], 'YA' => ['Я'], 'YU' => ['Ю'], 'ZH' => ['Ж'], ' ' => [" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "　"]];
	}
}