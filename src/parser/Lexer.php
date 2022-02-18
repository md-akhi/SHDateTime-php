<?php
    /**
	* 	In the name of Allah
	*
    * @package		Date and Time Related Extensions SH{ Shamsi Hijri, Solar Hijri, Iranian Hijri }
    * @author		Mohammad Amanalikhani (MD Amanalikhani, MD Akhi)
    * @link			http://docs.akhi.ir/php/SHDateTime
    * @license		https://www.gnu.org/licenses/agpl-3.0.en.html AGPL-3.0 License
    * @version		Release: 1.0.0
    */
	
/**
 * SHLexerConfig
 */
class SHLexerConfig
{
    /** @var TokenDefn[] */
    private $definitions = [];


	const SINGLE_QUOTE = '\'';
	const COMMA = ',';
	const DOT = '\.';
	const SPACE = "\s";
	const UNKNOWN_CHAR = '[^'.self::SPACE.self::DOT.']';
	
	/**
	 * tokenDefinitions
	 *
	 * @var array
	 */
	private $tokenDefinitions = [
		
		"DASH"  => '-',// MINUS
		"PLUS"  => '\+',
		"SLASH" => '\/',
		"COLON" => ':',
		"DOT"   => self::DOT,
		"COMMA" => self::COMMA,
		"SINGLE_QUOTE" => self::SINGLE_QUOTE,
		"SPACE" => self::SPACE,

		// ********* numeric rules **********

		"INT_00" => '00',
		"INT_01" => '01',
		"INT_02" => '02',
		"INT_03" => '03',
		"INT_04" => '04',
		"INT_05" => '05',
		"INT_06" => '06',
		"INT_07" => '07',
		"INT_08" => '08',
		"INT_09" => '09',
		"INT_10" => '10',
		"INT_11" => '11',
		"INT_12" => '12',
		"INT_13" => '13',
		"INT_14" => '14',
		"INT_15" => '15',
		"INT_16" => '16',
		"INT_17" => '17',
		"INT_18" => '18',
		"INT_19" => '19',
		"INT_20" => '20',
		"INT_21" => '21',
		"INT_22" => '22',
		"INT_23" => '23',
		"INT_24" => '24',
		"INT_25" => '25',
		"INT_26" => '26',
		"INT_27" => '27',
		"INT_28" => '28',
		"INT_29" => '29',
		"INT_30" => '30',
		"INT_31" => '31',
		"INT_32" => '32',
		"INT_33" => '33',
		"INT_34" => '34',
		"INT_35" => '35',
		"INT_36" => '36',
		"INT_37" => '37',
		"INT_38" => '38',
		"INT_39" => '39',
		"INT_40" => '40',
		"INT_41" => '41',
		"INT_42" => '42',
		"INT_43" => '43',
		"INT_44" => '44',
		"INT_45" => '45',
		"INT_46" => '46',
		"INT_47" => '47',
		"INT_48" => '48',
		"INT_49" => '49',
		"INT_50" => '50',
		"INT_51" => '51',
		"INT_52" => '52',
		"INT_53" => '53',
		"INT_54" => '54',
		"INT_55" => '55',
		"INT_56" => '56',
		"INT_57" => '57',
		"INT_58" => '58',
		"INT_59" => '59',
		"INT_60" => '60',
		"INT_61" => '61',
		"INT_62" => '62',
		"INT_63" => '63',
		"INT_64" => '64',
		"INT_65" => '65',
		"INT_66" => '66',
		"INT_67" => '67',
		"INT_68" => '68',
		"INT_69" => '69',
		"INT_70" => '70',
		"INT_71" => '71',
		"INT_72" => '72',
		"INT_73" => '73',
		"INT_74" => '74',
		"INT_75" => '75',
		"INT_76" => '76',
		"INT_77" => '77',
		"INT_78" => '78',
		"INT_79" => '79',
		"INT_80" => '80',
		"INT_81" => '81',
		"INT_82" => '82',
		"INT_83" => '83',
		"INT_84" => '84',
		"INT_85" => '85',
		"INT_86" => '86',
		"INT_87" => '87',
		"INT_88" => '88',
		"INT_89" => '89',
		"INT_90" => '90',
		"INT_91" => '91',
		"INT_92" => '92',
		"INT_93" => '93',
		"INT_94" => '94',
		"INT_95" => '95',
		"INT_96" => '96',
		"INT_97" => '97',
		"INT_98" => '98',
		"INT_99" => '99',

		"INT_0"  => '0',
		"INT_1"  => '1',
		"INT_2"  => '2',
		"INT_3"  => '3',
		"INT_4"  => '4',
		"INT_5"  => '5',
		"INT_6"  => '6',
		"INT_7"  => '7',
		"INT_8"  => '8',
		"INT_9"  => '9',

		"DIGIT" => '[0-9]+',//  $fragment

		"ONE"       => 'one',
		"TWO"       => 'two',
		"THREE"     => 'three',
		"FOUR"      => 'four',
		"FIVE"      => 'five',
		"SIX"       => 'six',
		"SEVEN"     => 'seven',
		"EIGHT"     => 'eight',
		"NINE"      => 'nine',
		"TEN"       => 'ten',
		"ELEVEN"    => 'eleven',
		"TWELVE"    => 'twelve',
		"THIRTEEN"  => 'thirteen',
		"FOURTEEN"  => 'fourteen',
		"FIFTEEN"   => 'fifteen',
		"SIXTEEN"   => 'sixteen',
		"SEVENTEEN" => 'seventeen',
		"EIGHTEEN"  => 'eightt?een',
		"NINETEEN"  => 'nineteen',
		"TWENTY"    => 'twenty',
		"THIRTY"    => 'thirty',

		"FIRST"          => 'first',
		"SECOND"         => 'seconds?|secs?',
		"THIRD"          => 'third',
		"FOURTH"         => 'fourth',
		"FIFTH"          => 'fifth',
		"SIXTH"          => 'sixth',
		"SEVENTH"        => 'seventh',
		"EIGHTH"         => 'eighth',
		"NINTH"          => 'ninth',
		"TENTH"          => 'tenth',
		"ELEVENTH"       => 'eleventh',
		"TWELFTH"        => 'twelfth',
		"THIRTEENTH"     => 'thirteenth',
		"FOURTEENTH"     => 'fourteenth',
		"FIFTEENTH"      => 'fifteenth',
		"SIXTEENTH"      => 'sixteenth',
		"SEVENTEENTH"    => 'seventeenth',
		"EIGHTEENTH"     => 'eighteenth',
		"NINETEENTH"     => 'nineteenth',
		"TWENTIETH"      => 'twentieth',
		"THIRTIETH"      => 'thirtieth',

		// ********** suffixes **********
		"ST" => 'st',
		"ND" => 'nd',
		"RD" => 'rd',
		"TH" => 'th',
		
		// ********** common rules **********
		
		"THIS"      => 'this',
		"OF"        => 'of',
		"LAST"      => 'last',
		"NEXT"      => 'next',
		"NOW"       => 'now',
		"AGO"       => 'ago',
		"BEFORE"    => 'before',
		"START"     => 'start',
		"END"       => 'end',
		"PREVIOUS"	=> 'previous',
        
		// ********** date rules ********** 

		"SATURDAY"  => 'saturday|sat',
		"SUNDAY"    => 'sunday|sun',
		"MONDAY"    => 'monday|mon',
		"TUESDAY"   => 'tuesday|tue',
		"WEDNESDAY" => 'wednesday|wed',
		"THURSDAY"  => 'thursday|thu',
		"FRIDAY"    => 'friday|fri|weekend',

		"HOUR"   => 'hours?',
		"MINUTE" => 'minutes?|min|mins',
		"DAY"    => 'days?',
		"WEEKDAY"  => 'weekdays?',
		"WEEK"   => 'weeks?',
		"MONTH"  => 'months?|mons?',
		"YEAR"   => 'years?',
		"FORTNIGHT" => 'forth?night',

		"TODAY"     => 'today',
		"TOMORROW"  => 'tomorrow',
		"TONIGHT"   => 'tonight',
		"YESTERDAY" => 'yesterday',
        
        "FARVARDIN"     =>  'farvardin|far',
        "ORDIBEHESHT"   =>  'ordibehesht|ord',
        "KHORDAD"       =>  'khordad|kho',
        "TIR"           =>  'tir',
        "AMORDAD"       =>  'a?mordad|amo|mor',
        "SHAHRIVAR"     =>  'shahrivar|sha',
        "MEHR"          =>  'mehr?',
        "ABAN"          =>  'aban?',
        "AZAR"          =>  'azar?',
        "DEY"           =>  'dey',
        "BAHMAN"        =>  'bahman|bah',
        "ESFAND"        =>  'es(f|p)and|esf|esp',
        
		// ********** time rules ********** 

		"AT"       => 'at|@',
		"BACK"    => 'back',
		"FRONT"     => 'front',
		"AM" 	=> 'a'.self::DOT.'?m'.self::DOT.'?',
		"PM" 	=> 'p'.self::DOT.'?m'.self::DOT.'?',

		"MIDNIGHT"  => 'mid-?night',
		"NOON"      => 'noon|after-?noon',
		"MORNING"   => 'morning',
		"EVENING"   => 'evening|eve',
		"NIGHT"     => 'night',

		"UTC"  => 'utc|gmt',

		"SIGN_TIME"  	=> 't',
		"SIGN_WEEK"  	=> 'w',

		// ********** date rules ********** 

        "INT_IV"        =>  'iv',   // 4
        "INT_VIII"      =>  'viii', // 8
        "INT_VII"       =>  'vii',  // 7
        "INT_VI"        =>  'vi',   // 6
        "INT_V"         =>  'v',    // 5
        "INT_IX"        =>  'ix',   // 9
        "INT_XII"       =>  'xii',  // 12
        "INT_XI"        =>  'xi',   // 11
        "INT_X"         =>  'x',    // 10
        "INT_III"       =>  'iii',  // 3
        "INT_II"        =>  'ii',   // 2
        "INT_I"         =>  'i',    // 1

		// ********** holiday specific **********

		// "FOOL"         => 'fool|fools|fool'.self::SINGLE_QUOTE.'s',
		// "BLACK"        => 'black',
		// "CHRISTMAS"    => '(christmas|xmas|x-mas)(es)?',
		// "COLUMBUS"     => 'columbus',
		// "EARTH"        => 'earth',
		// "EASTER"       => 'easter',
		// "FATHER"       => 'father|fathers|father'.self::SINGLE_QUOTE.'s',
		// "FLAG"         => 'flag',
		// "GOOD"         => 'good',
		// "GROUNDHOG"    => self::GROUND .self::SPACE.'?'.self::HOG .self::SINGLE_QUOTE.'?s?',
		// "HALLOWEEN"    => '(halloween|haloween)s?',
		// "INAUGURATION" => 'inauguration|inaugaration',
		// "INDEPENDENCE" => 'independence|independance',
		// "KWANZAA"      => '(kwanzaa?)s?',
		// "LABOR"        => 'labor',
		// "MLK"          => 'mlk|martin'.self::SPACE.'luther'.self::SPACE.'king('.self::COMMA.self::SPACE.'jr'.self::DOT.')?',
		// "MEMORIAL"     => 'memorial',
		// "MOTHER"       => 'mother'.self::SINGLE_QUOTE.'?s?',
		// "NEW"          => 'new',
		// "PALM"         => 'palm',
		// "PATRIOT"      => 'patriot'.self::SINGLE_QUOTE.'?s?',
		// "PRESIDENT"    => 'president'.self::SINGLE_QUOTE.'?s?',
		// "PATRICK"      => '(patrick|patty|paddy)'.self::SINGLE_QUOTE.'?s?',
		// "SAINT"        => 'saint',
		// "TAX"          => 'tax',
		// "THANKSGIVING" => 'thanksgivings?',
		// "ELECTION"     => 'election',
		// "VALENTINE"    => 'valentine'.self::SINGLE_QUOTE.'?s?',
		// "VETERAN"      => 'veteran'.self::SINGLE_QUOTE.'?s?',
		// "GROUND" 	   => self::GROUND,//  $fragment
		// "HOG"    	   => self::HOG,//  $fragment

		// ********** season specific **********

		"WINTER" => 'winters?',
		"FALL"   => 'falls?',
		"AUTUMN" => 'autumns?',
		"SPRING" => 'springs?',
		"SUMMER" => 'summers?',


		//"TZ"		=>	'\(?[A-Za-z]{3,6}\)?|[A-Z][a-z]+([_\/][A-Z][a-z]+)+',

		"UNKNOWN" => self::UNKNOWN_CHAR,
		"UNKNOWN_CHAR" => self::UNKNOWN_CHAR //  $fragment
	];

    /**
     * @param TokenDefn[] $tokenDefinitions
     */
    public function __construct()
    {
        foreach ($this->tokenDefinitions as $k => $v) {
            // if ($v instanceof SHTokenDefn) {
            //     $this->addTokenDefinition($v);
            // } elseif (is_string($k) && is_string($v)) {
                $this->addTokenDefinition(new SHTokenDefn($k, $v));
            // }
        }
    }

    /**
     * @param TokenDefn $tokenDefn
     */
    public function addTokenDefinition(SHTokenDefn $tokenDefn)
    {
        $this->definitions[] = $tokenDefn;
    }

    /**
     * @return TokenDefn[]
     */
    public function getTokenDefinitions()
    {
        return $this->definitions;
    }
}


class SHTokenDefn
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $regex;

    /**
     * @param string $name
     * @param string $regex
     * @param string $modifiers
     */
    public function __construct($name, $regex, $modifiers = 'i')
    {
        $this->name = $name;
        //$delimiter = $this->findDelimiter($regex);
        $this->regex = $regex;//sprintf('%s^%s%s%s', $delimiter, $regex, $delimiter, $modifiers);
        if (preg_match('/^'.$regex.'/i', '') === false) {// empty($regex)
            throw new InvalidArgumentException(sprintf('Invalid regex for token %s : %s', $name, $regex));
        }
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $regex
     *
     * @return string
     */
    private function findDelimiter($regex)
    {
        static $choices = ['/', '|', '#', '~', '@'];
        foreach ($choices as $choice) {
            if (strpos($regex, $choice) === false) {
                return $choice;
            }
        }

        throw new InvalidArgumentException(sprintf('Unable to determine delimiter for regex %s', $regex));
    }
}


class SHToken
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var int */
    protected $offset;

    /** @var int */
    protected $position;

    /**
     * @param string $name
     * @param string $value
     * @param string $offset
     * @param string $count
     */
    public function __construct($name, $value, $offset, $count)
    {
        $this->name = $name;
        $this->value = $value;
        $this->offset = $offset;
        $this->position = $count;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function is($token)
    {
		if(is_string($token)) {
            return $this->name === $token;
        }
        elseif($token instanceof self) {
            return $this->name === $token->getName();
        }
        throw new InvalidArgumentException('Expected string or Token');
    }
}


class SHLexer
{
    /** @var string */
    private $input;

    /** @var int */
    private $position;

    /** @var int */
    private $peek;

    /** @var Token[] */
    private $tokens;

    /** @var Token */
    private $lookahead;

    /** @var Token */
    private $token;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
		$this->setInput($input);
		$this->moveNext();
    }

    /**
     * @param LexerConfig $config
     * @param string      $input
     *
     * @return Token[]
     */
    public static function scan(SHLexerConfig $config, $input)
    {
        $tokens = [];
        $offset = 0;
        $position = 0;
        $matches = null;
        while (strlen($input)) {
            $anyMatch = false;
            foreach ($config->getTokenDefinitions() as $tokenDefinition) {
                if(preg_match('/^'.$tokenDefinition->getRegex().'/i', $input, $matches)) {
                    $str = $matches[0];
                    $len = strlen($str);
                    if (strlen($tokenDefinition->getName()) > 0) {
                        $tokens[] = new SHToken($tokenDefinition->getName(), $str, $offset, $position);
                        ++$position;
                    }
                    $input = substr($input, $len);
                    $anyMatch = true;
                    $offset += $len;
                    break;
                }
            }
            if (!$anyMatch) {
                throw new UnknownTokenException(sprintf('At offset %s: %s', $offset, substr($input, 0, 16).'...'));
            }
        }

        return $tokens;
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return Token
     */
    public function getLookahead()
    {
        return $this->lookahead;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    public function setInput($input)
    {
        $this->input = $input;
        $this->reset();
        $this->tokens = static::scan(new SHLexerConfig(), $input);
    }

    public function reset()
    {
        $this->position = 0;
        $this->peek = 0;
        $this->token = null;
        $this->lookahead = null;
    }

    public function resetPeek()
    {
        $this->peek = 0;
    }

    public function resetPosition($position = 0)
    {
        $this->position = $position-1;
		$this->moveNext();
    }

    /**
     * @param string $tokenName
     *
     * @return bool
     */
    public function isNextToken($tokenName)
    {
        return null !== $this->lookahead && $this->lookahead->getName() === $tokenName;
    }

    /**
     * @param string[] $tokenNames
     *
     * @return bool
     */
    public function isNextTokenAny(array $tokenNames)
    {
        return null !== $this->lookahead && in_array($this->lookahead->getName(), $tokenNames, true);
    }

    /**
     * @return bool
     */
    public function moveNext()
    {
        $this->peek = 0;
        $this->token = $this->lookahead;
        $this->lookahead = (isset($this->tokens[$this->position]))
            ? $this->tokens[$this->position++]
            : null;

        return $this->lookahead !== null;
    }

    /**
     * @param string $tokenName
     */
    public function skipUntil($tokenName)
    {
        while ($this->lookahead !== null && $this->lookahead->getName() !== $tokenName) {
            $this->moveNext();
        }
    }

    /**
     * @param string[] $tokenNames
     */
    public function skipTokens(array $tokenNames)
    {
        while ($this->lookahead !== null && in_array($this->lookahead->getName(), $tokenNames, true)) {
            $this->moveNext();
        }
    }

    /**
     * Moves the lookahead token forward.
     *
     * @return null|Token
     */
    public function peek()
    {
        if (isset($this->tokens[$this->position + $this->peek])) {
            return $this->tokens[$this->position + $this->peek++];
        } else {
            return null;
        }
    }

    /**
     * @param string[] $tokenNames
     *
     * @return null|Token
     */
    public function peekWhileTokens(array $tokenNames)
    {
        while ($token = $this->peek()) {
            if (!in_array($token->getName(), $tokenNames, true)) {
                break;
            }
        }

        return $token;
    }

    /**
     * Peeks at the next token, returns it and immediately resets the peek.
     *
     * @return null|Token
     */
    public function glimpse()
    {
        $peek = $this->peek();
        $this->peek = 0;

        return $peek;
    }
}









	