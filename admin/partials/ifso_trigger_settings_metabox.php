<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// get trigger rules data
$data = array();
global $data_versions, $available_pages, $post_status, $languages, $data_rules, $displayClosedFeature, $freeTriggers, $lockedConditionBox, $isLicenseValid;
$data_default = get_post_meta( $post->ID, 'ifso_trigger_default', true );
$data_rules_json = get_post_meta( $post->ID, 'ifso_trigger_rules', true );
$data_rules = json_decode($data_rules_json, true);
$data_versions = get_post_meta( $post->ID, 'ifso_trigger_version', false );

$languages = array(
        array('en', 'eng', 'eng', 'eng', 'English', 'English'),
        array('he', 'heb', 'heb', 'heb', 'Hebrew', 'עברית'),
        array('ab', 'abk', 'abk', 'abk', 'Abkhaz', 'аҧсуа бызшәа, аҧсшәа'),
        array('aa', 'aar', 'aar', 'aar', 'Afar', 'Afaraf'),
        array('af', 'afr', 'afr', 'afr', 'Afrikaans', 'Afrikaans'),
        array('ak', 'aka', 'aka', 'aka', 'Akan', 'Akan'),
        array('sq', 'sqi', 'alb', 'sqi', 'Albanian', 'Shqip'),
        array('am', 'amh', 'amh', 'amh', 'Amharic', 'አማርኛ'),
        array('ar', 'ara', 'ara', 'ara', 'Arabic', 'العربية'),
        array('an', 'arg', 'arg', 'arg', 'Aragonese', 'aragonés'),
        array('hy', 'hye', 'arm', 'hye', 'Armenian', 'Հայերեն'),
        array('as', 'asm', 'asm', 'asm', 'Assamese', 'অসমীয়া'),
        array('av', 'ava', 'ava', 'ava', 'Avaric', 'авар мацӀ, магӀарул мацӀ'),
        array('ae', 'ave', 'ave', 'ave', 'Avestan', 'avesta'),
        array('ay', 'aym', 'aym', 'aym', 'Aymara', 'aymar aru'),
        array('az', 'aze', 'aze', 'aze', 'Azerbaijani', 'azərbaycan dili'),
        array('bm', 'bam', 'bam', 'bam', 'Bambara', 'bamanankan'),
        array('ba', 'bak', 'bak', 'bak', 'Bashkir', 'башҡорт теле'),
        array('eu', 'eus', 'baq', 'eus', 'Basque', 'euskara, euskera'),
        array('be', 'bel', 'bel', 'bel', 'Belarusian', 'беларуская мова'),
        array('bn', 'ben', 'ben', 'ben', 'Bengali, Bangla', 'বাংলা'),
        array('bh', 'bih', 'bih', '', 'Bihari', 'भोजपुरी'),
        array('bi', 'bis', 'bis', 'bis', 'Bislama', 'Bislama'),
        array('bs', 'bos', 'bos', 'bos', 'Bosnian', 'bosanski jezik'),
        array('br', 'bre', 'bre', 'bre', 'Breton', 'brezhoneg'),
        array('bg', 'bul', 'bul', 'bul', 'Bulgarian', 'български език'),
        array('my', 'mya', 'bur', 'mya', 'Burmese', 'ဗမာစာ'),
        array('ca', 'cat', 'cat', 'cat', 'Catalan', 'català'),
        array('ch', 'cha', 'cha', 'cha', 'Chamorro', 'Chamoru'),
        array('ce', 'che', 'che', 'che', 'Chechen', 'нохчийн мотт'),
        array('ny', 'nya', 'nya', 'nya', 'Chichewa, Chewa, Nyanja', 'chiCheŵa, chinyanja'),
        array('zh', 'zho', 'chi', 'zho', 'Chinese', '中文 (Zhōngwén), 汉语, 漢語'),
        array('cv', 'chv', 'chv', 'chv', 'Chuvash', 'чӑваш чӗлхи'),
        array('kw', 'cor', 'cor', 'cor', 'Cornish', 'Kernewek'),
        array('co', 'cos', 'cos', 'cos', 'Corsican', 'corsu, lingua corsa'),
        array('cr', 'cre', 'cre', 'cre', 'Cree', 'ᓀᐦᐃᔭᐍᐏᐣ'),
        array('hr', 'hrv', 'hrv', 'hrv', 'Croatian', 'hrvatski jezik'),
        array('cs', 'ces', 'cze', 'ces', 'Czech', 'čeština, český jazyk'),
        array('da', 'dan', 'dan', 'dan', 'Danish', 'dansk'),
        array('dv', 'div', 'div', 'div', 'Divehi, Dhivehi, Maldivian', 'ދިވެހި'),
        array('nl', 'nld', 'dut', 'nld', 'Dutch', 'Nederlands, Vlaams'),
        array('dz', 'dzo', 'dzo', 'dzo', 'Dzongkha', 'རྫོང་ཁ'),
        array('eo', 'epo', 'epo', 'epo', 'Esperanto', 'Esperanto'),
        array('et', 'est', 'est', 'est', 'Estonian', 'eesti, eesti keel'),
        array('ee', 'ewe', 'ewe', 'ewe', 'Ewe', 'Eʋegbe'),
        array('fo', 'fao', 'fao', 'fao', 'Faroese', 'føroyskt'),
        array('fj', 'fij', 'fij', 'fij', 'Fijian', 'vosa Vakaviti'),
        array('fi', 'fin', 'fin', 'fin', 'Finnish', 'suomi, suomen kieli'),
        array('fr', 'fra', 'fre', 'fra', 'French', 'français, langue française'),
        array('ff', 'ful', 'ful', 'ful', 'Fula, Fulah, Pulaar, Pular', 'Fulfulde, Pulaar, Pular'),
        array('gl', 'glg', 'glg', 'glg', 'Galician', 'galego'),
        array('ka', 'kat', 'geo', 'kat', 'Georgian', 'ქართული'),
        array('de', 'deu', 'ger', 'deu', 'German', 'Deutsch'),
        array('el', 'ell', 'gre', 'ell', 'Greek', 'ελληνικά'),
        array('gn', 'grn', 'grn', 'grn', 'Guaraní', 'Avañe\'ẽ'),
        array('gu', 'guj', 'guj', 'guj', 'Gujarati', 'ગુજરાતી'),
        array('ht', 'hat', 'hat', 'hat', 'Haitian, Haitian Creole', 'Kreyòl ayisyen'),
        array('ha', 'hau', 'hau', 'hau', 'Hausa', '(Hausa) هَوُسَ'),
        array('hz', 'her', 'her', 'her', 'Herero', 'Otjiherero'),
        array('hi', 'hin', 'hin', 'hin', 'Hindi', 'हिन्दी, हिंदी'),
        array('ho', 'hmo', 'hmo', 'hmo', 'Hiri Motu', 'Hiri Motu'),
        array('hu', 'hun', 'hun', 'hun', 'Hungarian', 'magyar'),
        array('ia', 'ina', 'ina', 'ina', 'Interlingua', 'Interlingua'),
        array('id', 'ind', 'ind', 'ind', 'Indonesian', 'Bahasa Indonesia'),
        array('ie', 'ile', 'ile', 'ile', 'Interlingue', 'Originally called Occidental; then Interlingue after WWII'),
        array('ga', 'gle', 'gle', 'gle', 'Irish', 'Gaeilge'),
        array('ig', 'ibo', 'ibo', 'ibo', 'Igbo', 'Asụsụ Igbo'),
        array('ik', 'ipk', 'ipk', 'ipk', 'Inupiaq', 'Iñupiaq, Iñupiatun'),
        array('io', 'ido', 'ido', 'ido', 'Ido', 'Ido'),
        array('is', 'isl', 'ice', 'isl', 'Icelandic', 'Íslenska'),
        array('it', 'ita', 'ita', 'ita', 'Italian', 'italiano'),
        array('iu', 'iku', 'iku', 'iku', 'Inuktitut', 'ᐃᓄᒃᑎᑐᑦ'),
        array('ja', 'jpn', 'jpn', 'jpn', 'Japanese', '日本語 (にほんご)'),
        array('jv', 'jav', 'jav', 'jav', 'Javanese', 'basa Jawa'),
        array('kl', 'kal', 'kal', 'kal', 'Kalaallisut, Greenlandic', 'kalaallisut, kalaallit oqaasii'),
        array('kn', 'kan', 'kan', 'kan', 'Kannada', 'ಕನ್ನಡ'),
        array('kr', 'kau', 'kau', 'kau', 'Kanuri', 'Kanuri'),
        array('ks', 'kas', 'kas', 'kas', 'Kashmiri', 'कश्मीरी, كشميري‎'),
        array('kk', 'kaz', 'kaz', 'kaz', 'Kazakh', 'қазақ тілі'),
        array('km', 'khm', 'khm', 'khm', 'Khmer', 'ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ'),
        array('ki', 'kik', 'kik', 'kik', 'Kikuyu, Gikuyu', 'Gĩkũyũ'),
        array('rw', 'kin', 'kin', 'kin', 'Kinyarwanda', 'Ikinyarwanda'),
        array('ky', 'kir', 'kir', 'kir', 'Kyrgyz', 'Кыргызча, Кыргыз тили'),
        array('kv', 'kom', 'kom', 'kom', 'Komi', 'коми кыв'),
        array('kg', 'kon', 'kon', 'kon', 'Kongo', 'Kikongo'),
        array('ko', 'kor', 'kor', 'kor', 'Korean', '한국어, 조선어'),
        array('ku', 'kur', 'kur', 'kur', 'Kurdish', 'Kurdî, كوردی‎'),
        array('kj', 'kua', 'kua', 'kua', 'Kwanyama, Kuanyama', 'Kuanyama'),
        array('la', 'lat', 'lat', 'lat', 'Latin', 'latine, lingua latina'),
        array('', '', '', 'lld', 'Ladin', 'ladin, lingua ladina'),
        array('lb', 'ltz', 'ltz', 'ltz', 'Luxembourgish, Letzeburgesch', 'Lëtzebuergesch'),
        array('lg', 'lug', 'lug', 'lug', 'Ganda', 'Luganda'),
        array('li', 'lim', 'lim', 'lim', 'Limburgish, Limburgan, Limburger', 'Limburgs'),
        array('ln', 'lin', 'lin', 'lin', 'Lingala', 'Lingála'),
        array('lo', 'lao', 'lao', 'lao', 'Lao', 'ພາສາລາວ'),
        array('lt', 'lit', 'lit', 'lit', 'Lithuanian', 'lietuvių kalba'),
        array('lu', 'lub', 'lub', 'lub', 'Luba-Katanga', 'Tshiluba'),
        array('lv', 'lav', 'lav', 'lav', 'Latvian', 'latviešu valoda'),
        array('gv', 'glv', 'glv', 'glv', 'Manx', 'Gaelg, Gailck'),
        array('mk', 'mkd', 'mac', 'mkd', 'Macedonian', 'македонски јазик'),
        array('mg', 'mlg', 'mlg', 'mlg', 'Malagasy', 'fiteny malagasy'),
        array('ms', 'msa', 'may', 'msa', 'Malay', 'bahasa Melayu, بهاس ملايو‎'),
        array('ml', 'mal', 'mal', 'mal', 'Malayalam', 'മലയാളം'),
        array('mt', 'mlt', 'mlt', 'mlt', 'Maltese', 'Malti'),
        array('mi', 'mri', 'mao', 'mri', 'Māori', 'te reo Māori'),
        array('mr', 'mar', 'mar', 'mar', 'Marathi (Marāṭhī)', 'मराठी'),
        array('mh', 'mah', 'mah', 'mah', 'Marshallese', 'Kajin M̧ajeļ'),
        array('mn', 'mon', 'mon', 'mon', 'Mongolian', 'монгол'),
        array('na', 'nau', 'nau', 'nau', 'Nauru', 'Ekakairũ Naoero'),
        array('nv', 'nav', 'nav', 'nav', 'Navajo, Navaho', 'Diné bizaad'),
        array('nd', 'nde', 'nde', 'nde', 'Northern Ndebele', 'isiNdebele'),
        array('ne', 'nep', 'nep', 'nep', 'Nepali', 'नेपाली'),
        array('ng', 'ndo', 'ndo', 'ndo', 'Ndonga', 'Owambo'),
        array('nb', 'nob', 'nob', 'nob', 'Norwegian Bokmål', 'Norsk bokmål'),
        array('nn', 'nno', 'nno', 'nno', 'Norwegian Nynorsk', 'Norsk nynorsk'),
        array('no', 'nor', 'nor', 'nor', 'Norwegian', 'Norsk'),
        array('ii', 'iii', 'iii', 'iii', 'Nuosu', 'ꆈꌠ꒿ Nuosuhxop'),
        array('nr', 'nbl', 'nbl', 'nbl', 'Southern Ndebele', 'isiNdebele'),
        array('oc', 'oci', 'oci', 'oci', 'Occitan', 'occitan, lenga d\'òc'),
        array('oj', 'oji', 'oji', 'oji', 'Ojibwe, Ojibwa', 'ᐊᓂᔑᓈᐯᒧᐎᓐ'),
        array('cu', 'chu', 'chu', 'chu', 'Old Church Slavonic, Church Slavonic, Old Bulgarian', 'ѩзыкъ словѣньскъ'),
        array('om', 'orm', 'orm', 'orm', 'Oromo', 'Afaan Oromoo'),
        array('or', 'ori', 'ori', 'ori', 'Oriya', 'ଓଡ଼ିଆ'),
        array('os', 'oss', 'oss', 'oss', 'Ossetian, Ossetic', 'ирон æвзаг'),
        array('pa', 'pan', 'pan', 'pan', 'Panjabi, Punjabi', 'ਪੰਜਾਬੀ, پنجابی‎'),
        array('pi', 'pli', 'pli', 'pli', 'Pāli', 'पाऴि'),
        array('fa', 'fas', 'per', 'fas', 'Persian (Farsi)', 'فارسی'),
        array('pl', 'pol', 'pol', 'pol', 'Polish', 'język polski, polszczyzna'),
        array('ps', 'pus', 'pus', 'pus', 'Pashto, Pushto', 'پښتو'),
        array('pt', 'por', 'por', 'por', 'Portuguese', 'português'),
        array('qu', 'que', 'que', 'que', 'Quechua', 'Runa Simi, Kichwa'),
        array('rm', 'roh', 'roh', 'roh', 'Romansh', 'rumantsch grischun'),
        array('rn', 'run', 'run', 'run', 'Kirundi', 'Ikirundi'),
        array('ro', 'ron', 'rum', 'ron', 'Romanian', 'limba română'),
        array('ru', 'rus', 'rus', 'rus', 'Russian', 'Русский'),
        array('sa', 'san', 'san', 'san', 'Sanskrit (Saṁskṛta)', 'संस्कृतम्'),
        array('sc', 'srd', 'srd', 'srd', 'Sardinian', 'sardu'),
        array('sd', 'snd', 'snd', 'snd', 'Sindhi', 'सिन्धी, سنڌي، سندھی‎'),
        array('se', 'sme', 'sme', 'sme', 'Northern Sami', 'Davvisámegiella'),
        array('sm', 'smo', 'smo', 'smo', 'Samoan', 'gagana fa\'a Samoa'),
        array('sg', 'sag', 'sag', 'sag', 'Sango', 'yângâ tî sängö'),
        array('sr', 'srp', 'srp', 'srp', 'Serbian', 'српски језик'),
        array('gd', 'gla', 'gla', 'gla', 'Scottish Gaelic, Gaelic', 'Gàidhlig'),
        array('sn', 'sna', 'sna', 'sna', 'Shona', 'chiShona'),
        array('si', 'sin', 'sin', 'sin', 'Sinhala, Sinhalese', 'සිංහල'),
        array('sk', 'slk', 'slo', 'slk', 'Slovak', 'slovenčina, slovenský jazyk'),
        array('sl', 'slv', 'slv', 'slv', 'Slovene', 'slovenski jezik, slovenščina'),
        array('so', 'som', 'som', 'som', 'Somali', 'Soomaaliga, af Soomaali'),
        array('st', 'sot', 'sot', 'sot', 'Southern Sotho', 'Sesotho'),
        array('es', 'spa', 'spa', 'spa', 'Spanish', 'español'),
        array('su', 'sun', 'sun', 'sun', 'Sundanese', 'Basa Sunda'),
        array('sw', 'swa', 'swa', 'swa', 'Swahili', 'Kiswahili'),
        array('ss', 'ssw', 'ssw', 'ssw', 'Swati', 'SiSwati'),
        array('sv', 'swe', 'swe', 'swe', 'Swedish', 'svenska'),
        array('ta', 'tam', 'tam', 'tam', 'Tamil', 'தமிழ்'),
        array('te', 'tel', 'tel', 'tel', 'Telugu', 'తెలుగు'),
        array('tg', 'tgk', 'tgk', 'tgk', 'Tajik', 'тоҷикӣ, toçikī, تاجیکی‎'),
        array('th', 'tha', 'tha', 'tha', 'Thai', 'ไทย'),
        array('ti', 'tir', 'tir', 'tir', 'Tigrinya', 'ትግርኛ'),
        array('bo', 'bod', 'tib', 'bod', 'Tibetan Standard, Tibetan, Central', 'བོད་ཡིག'),
        array('tk', 'tuk', 'tuk', 'tuk', 'Turkmen', 'Türkmen, Түркмен'),
        array('tl', 'tgl', 'tgl', 'tgl', 'Tagalog', 'Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔'),
        array('tn', 'tsn', 'tsn', 'tsn', 'Tswana', 'Setswana'),
        array('to', 'ton', 'ton', 'ton', 'Tonga (Tonga Islands)', 'faka Tonga'),
        array('tr', 'tur', 'tur', 'tur', 'Turkish', 'Türkçe'),
        array('ts', 'tso', 'tso', 'tso', 'Tsonga', 'Xitsonga'),
        array('tt', 'tat', 'tat', 'tat', 'Tatar', 'татар теле, tatar tele'),
        array('tw', 'twi', 'twi', 'twi', 'Twi', 'Twi'),
        array('ty', 'tah', 'tah', 'tah', 'Tahitian', 'Reo Tahiti'),
        array('ug', 'uig', 'uig', 'uig', 'Uyghur', 'ئۇيغۇرچە‎, Uyghurche'),
        array('uk', 'ukr', 'ukr', 'ukr', 'Ukrainian', 'українська мова'),
        array('ur', 'urd', 'urd', 'urd', 'Urdu', 'اردو'),
        array('uz', 'uzb', 'uzb', 'uzb', 'Uzbek', 'Oʻzbek, Ўзбек, أۇزبېك‎'),
        array('ve', 'ven', 'ven', 'ven', 'Venda', 'Tshivenḓa'),
        array('vi', 'vie', 'vie', 'vie', 'Vietnamese', 'Việt Nam'),
        array('vo', 'vol', 'vol', 'vol', 'Volapük', 'Volapük'),
        array('wa', 'wln', 'wln', 'wln', 'Walloon', 'walon'),
        array('cy', 'cym', 'wel', 'cym', 'Welsh', 'Cymraeg'),
        array('wo', 'wol', 'wol', 'wol', 'Wolof', 'Wollof'),
        array('fy', 'fry', 'fry', 'fry', 'Western Frisian', 'Frysk'),
        array('xh', 'xho', 'xho', 'xho', 'Xhosa', 'isiXhosa'),
        array('yi', 'yid', 'yid', 'yid', 'Yiddish', 'ייִדיש'),
        array('yo', 'yor', 'yor', 'yor', 'Yoruba', 'Yorùbá'),
        array('za', 'zha', 'zha', 'zha', 'Zhuang, Chuang', 'Saɯ cueŋƅ, Saw cuengh'),
        array('zu', 'zul', 'zul', 'zul', 'Zulu', 'isiZulu')
    );
// get all available pages
$args = array(
    'sort_order' => 'asc',
    'sort_column' => 'post_title',
    'hierarchical' => 1,
    'child_of' => 0,
    'post_type' => 'page',
    'post_status' => 'publish',
    'suppress_filters' => true
); 
$available_pages = get_pages($args);
$post_status = get_post_status();

/* IfSo License Begin */
$status  = get_option( 'edd_ifso_license_status' );

$isLicenseValid = ($status !== false && $status == 'valid') ? true : false;

$displayClosedFeature = " *";

if ($isLicenseValid) $displayClosedFeature = "";

$freeTriggers = ["Device", "User-Behavior"];

// $lockedConditionBox = '
//     <div class="locked-condition-box">
//         <div class="text">
//             This is locked condition. Click here to unlock all features.
//         </div>

//         <a href="#" class="unlock-button">
//             <i class="fa fa-unlock-alt" aria-hidden="true"></i> Unlock Now
//         </a>
//     </div>
// ';

$lockedConditionBox = '
    <div class="get-license clearfix" style="margin-top:0;">
        <div class="text">
            This condition is locked. Click here to get your free 1 year license key and unlock all features.
        </div>
        <a href="https://goo.gl/ZBpXqD" class="get-license-btn" target="_blank">GET YOUR FREE LICENSE KEY<i class="fa fa-play" style="margin-left:10px;" aria-hidden="true"></i>
        </a>
    </div>
';

/* Ifso License End */
?>

<?php 
function get_rule_item($index, $rule=array(), $is_template = false) {
    global $data_versions, $available_pages, $post_status, $languages, $data_rules, $displayClosedFeature, $freeTriggers, $lockedConditionBox, $isLicenseValid;

    if($is_template) {
        $current_version_index = 'index_placeholder';
        $current_version_count = '{version_number}';
        $current_datetime_count = '{datetime_number}';
        $current_version_count_char = '{version_char}';
        $current_instructions = '{version_instructions}';
    }
    else {
        $current_version_index = $index; //+1; // Removed the +1
        $current_version_count = $current_version_index+1;
        $current_datetime_count = "";
        $current_version_count_char = chr(64 + $current_version_index+1);

        if ($index == 0) {
        	// First one!
        	$current_instructions = "Select a condition, the content will be displayed only if it&apos;s met";
        } else if ($index == 1) {
        	// Start by singulars
        	$current_instructions = "This version will appear only if version A is not realized";
        } else {
        	// Start by many
        	$prevChar = chr(64 + $current_version_index);

        	$current_instructions = "This version will appear only if varsions A-".$prevChar." are not realized";
        }
    }
?>
    <div data-repeater-list="group-version" class="rule-item reapeater-item reapeater-item-cloned <?php echo (!$is_template) ? 'reapeater-item-cloned-loaded' : ''; ?>">
        <div class="row rule-wrap">
            <div class="col-xs-12 rule-toolbar-wrap <?php echo (!$isLicenseValid && (!$is_template && !empty($rule['trigger_type']) && !in_array($rule['trigger_type'], $freeTriggers) || ($rule['trigger_type'] == "User-Behavior" && !in_array($rule['User-Behavior'], array('LoggedIn', 'LoggedOut', 'Logged'))))) ? 'rule-toolbar-wrap-clear' : ''; ?> <?php echo " ".(trim($rule['trigger_type']) != "")?"Y":"N"." "; ?> <?php echo (isset($rule['freeze-mode']) && $rule['freeze-mode'] == "true") ? "freeze-overlay-active-container" : ""; ?>">
                <h3>
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'images/IfSo_logo25X8.png'; ?>" />
                    <!--<span class="version-count"><?php echo $current_version_count; ?></span>-->
                    <span class="version-alpha"><?php _e('Personalized Content – Version', 'if-so'); ?> <?php echo __($current_version_count_char); ?></span> 
                </h3>
                <button type="button" data-repeater-delete class="repeater-delete btn btn-delete"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                <!-- begin freeze mode section -->
                <?php if ((isset($rule['freeze-mode']) &&
                          $rule['freeze-mode'] == "true")): ?>
                    <div class="btn btn-freeze ifso-freezemode">
                        <span class="text"><i class="fa fa-pause" aria-hidden="true"></i></span>
                    </div>
                <?php else: ?>
                    <div class="btn btn-freeze ifso-freezemode">
                        <span class="text"><i class="fa fa-play" aria-hidden="true"></i></span>
                    </div>
                <?php endif; ?>

                <input type="hidden" class="freeze-mode-val" name="repeater[<?php echo $current_version_index; ?>][freeze-mode]" <?php echo isset($rule['freeze-mode']) ? "value='{$rule['freeze-mode']}'" : ''; ?> />
                <!-- end freeze mode section -->

                <div class="col-xs-12 form-group locked-condition-box-container referrer-custom <?php echo (!$isLicenseValid && (!$is_template && isset($rule['trigger_type']) && !in_array($rule['trigger_type'], $freeTriggers) || ($rule['trigger_type'] == "User-Behavior" && !in_array($rule['User-Behavior'], array('LoggedIn', 'LoggedOut', 'Logged'))))) ? 'show-selection' : '' ?>" data-field="locked-box">

                    <?php if(!$isLicenseValid): ?>

                        <?php echo $lockedConditionBox; ?>

                    <?php endif; ?>

                </div>
            </div>

            <div class="col-md-3">
                <?php if(false):/*$post_status != 'publish'):*/ ?>
                    <p class="initial-instructions"><b><?php _e('', 'if-so'); ?>2</b> <?php _e("Choose a condition and set the content to display if it's met", 'if-so'); ?></p>
                <?php endif; ?>
                <p class="versioninstructions"><?php echo $current_instructions; ?>:</p>
                <div class="form-group">
                    <!--<label for="repeatable_editor_repeatable_editor_content"><?php _e( 'Trigger', 'if-so' ); ?></label><br>-->
                    <select name="repeater[<?php echo $current_version_index; ?>][trigger_type]" class="form-control trigger-type">
                        <option value="" data-reset="referrer-selection|referrer-custom|url-custom|page-selection|custom-url-display|AB-Testing|ab-testing-selection|ab-testing-custom|ab-testing-selection|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|advertising-platforms-selection|locked-box"><?php _e('Select a Condition', 'if-so'); ?></option>


                        <option value="Device" <?php echo ($rule['trigger_type'] == 'Device') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|common-referrers|locked-box|user-behavior-logged-selection|advertising-platforms-selection"
                             data-next-field="user-behavior-device"><?php _e('Device', 'if-so'); ?></option>

                        <option value="User-Behavior" <?php echo ($rule['trigger_type'] == 'User-Behavior') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|advertising-platforms-selection|locked-box" data-next-field="user-behavior-selection|user-behavior-logged-selection">
                            <?php _e('User Behavior', 'if-so'); ?>
                        </option>

                        <option value="referrer" <?php echo ($rule['trigger_type'] == 'referrer') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|custom-url-display|AB-Testing|ab-testing-selection|User-Behavior|ab-testing-selection|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|user-behavior-logged-selection|advertising-platforms-selection" data-next-field="referrer-selection|referrer-custom|locked-box">
                            <?php _e('Referrer Source'.$displayClosedFeature, 'if-so'); ?>
                        </option>
                        <option value="advertising-platforms" <?php echo ($rule['trigger_type'] == 'advertising-platforms') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|User-Behavior|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|user-behavior-logged-selection|url-custom" data-next-field="advertising-platforms-selection|locked-box">
                            <?php _e('Advertising Platforms'.$displayClosedFeature, 'if-so'); ?>
                        </option>
                        <option value="url" <?php echo ($rule['trigger_type'] == 'url') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|User-Behavior|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|user-behavior-logged-selection|advertising-platforms-selection" data-next-field="url-custom|locked-box">
                            <?php _e('Dynamic Link (Query string)'.$displayClosedFeature, 'if-so'); ?>
                        </option>
                        <option value="AB-Testing" <?php echo ($rule['trigger_type'] == 'AB-Testing') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|time-date-selection|schedule-selection|times-dates-schedules-selections|time-date-pick-start-date|time-date-pick-end-date|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|user-behavior-logged-selection|advertising-platforms-selection" data-next-field="ab-testing-selection|locked-box">
                            <?php _e('A/B Testing'.$displayClosedFeature, 'if-so'); ?>

                        <option value="Time-Date" <?php echo ($rule['trigger_type'] == 'Time-Date') ? 'SELECTED' : ''; ?> data-reset="referrer-selection|referrer-custom|ab-testing-custom|url-custom|page-selection|AB-Testing|ab-testing-selection|custom-url-display|User-Behavior|user-behavior-returning|user-behavior-retn-custom|user-behavior-selection|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|common-referrers|user-behavior-selection|user-behavior-logged-selection|advertising-platforms-selection" data-next-field="time-date-selection|times-dates-schedules-selections|locked-box">
                            <?php _e('Time & Date'.$displayClosedFeature, 'if-so'); ?>
                        </option>

                    </select>
                </div>
                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][trigger]" data-field="referrer-selection" class="form-control referrer-selection <?php echo (!empty($rule['trigger']) && $rule['trigger_type'] == 'referrer') ? 'show-selection' : ''; ?>">
                        <!--<option value="" data-reset="referrer-custom|url-custom|page-selection|common-referrers"><?php _e('Choose a Referrer', 'if-so'); ?></option>-->
                        <option value="custom" <?php echo ($rule['trigger'] == 'custom') ? 'SELECTED' : ''; ?> data-reset="common-referrers|page-selection|url-custom" data-next-field="referrer-custom|locked-box"><?php _e('URL', 'if-so'); ?></option>
                        <option value="page-on-website" <?php echo ($rule['trigger'] == 'page-on-website') ? 'SELECTED' : ''; ?> data-next-field="page-selection|locked-box" data-reset="common-referrers|referrer-custom|url-custom"><?php _e('Page on My Website', 'if-so'); ?></option>
                        <option value="common-referrers" <?php echo ($rule['trigger'] == 'common-referrers') ? 'SELECTED' : ''; ?> data-next-field="common-referrers|locked-box" data-reset="referrer-custom|url-custom|page-selection"><?php _e('Common Referrers', 'if-so'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][AB-Testing]" data-field="ab-testing-selection|locked-box" class="form-control ab-testing <?php echo (!empty($rule['AB-Testing']) && ($rule['trigger_type'] == 'AB-Testing')) ? 'show-selection' : ''; ?>">
                        <option value="50%" <?php echo ($rule['AB-Testing'] == '50%') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection"><?php _e('50% of the sessions', 'if-so'); ?></option>
                        <option value="25%" <?php echo ($rule['AB-Testing'] == '25%') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection"><?php _e('25% of the sessions', 'if-so'); ?></option>
                        <option value="33%" <?php echo ($rule['AB-Testing'] == '33%') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection"><?php _e('33% of the sessions', 'if-so'); ?></option>
                        <option value="75%" <?php echo ($rule['AB-Testing'] == '75%') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection
                        "><?php _e('75% of the sessions', 'if-so'); ?></option>
                    </select>

                    <div class="ab-testing-custom-sessions-display <?php echo (!empty($rule['AB-Testing']) && ($rule['trigger_type'] == 'AB-Testing')) ? 'show-selection' : ''; ?>" data-field="ab-testing-selection">
                        <p><?php _e('Limit (Optional):', 'if-so'); ?></p>
                    </div>

                    <input type="hidden" name="repeater[<?php echo $current_version_index; ?>][saved_number_of_views]" <?php echo ((!empty($rule['number_of_views']) || $rule['number_of_views'] == 0) && ($rule['trigger_type'] == 'AB-Testing')) ? "value='{$rule['number_of_views']}'" : ''; ?> />
                    
                    <select name="repeater[<?php echo $current_version_index; ?>][ab-testing-sessions]" data-field="ab-testing-selection" class="form-control ab-testing <?php echo (!empty($rule['ab-testing-sessions']) && ($rule['trigger_type'] == 'AB-Testing')) ? 'show-selection' : ''; ?>">
                        <option value="Unlimited" data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('Unlimited', 'if-so'); ?></option>
                        <option value="100" <?php echo ($rule['ab-testing-sessions'] == '100') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('100 Sessions', 'if-so'); ?></option>
                        <option value="200" <?php echo ($rule['ab-testing-sessions'] == '200') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('200 Sessions', 'if-so'); ?></option>
                        <option value="500" <?php echo ($rule['ab-testing-sessions'] == '500') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('500 Sessions', 'if-so'); ?></option>
                        <option value="1000" <?php echo ($rule['ab-testing-sessions'] == '1000') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('1000 Sessions', 'if-so'); ?></option>
                        <option value="2000" <?php echo ($rule['ab-testing-sessions'] == '2000') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom"><?php _e('2000 Sessions', 'if-so'); ?></option>
                        <option value="Custom" <?php echo ($rule['ab-testing-sessions'] == 'Custom') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom" data-next-field="ab-testing-custom|locked-box"><?php _e('Custom', 'if-so'); ?></option>
                    </select>




                </div>


                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][Time-Date-Schedule-Selection]" data-field="times-dates-schedules-selections" class="form-control ab-testing ifso-pick-start-date <?php echo ($rule['trigger_type'] == 'Time-Date') ? 'show-selection' : ''; ?>">
                        <option value="Start-End-Date" <?php echo ($rule['Time-Date-Schedule-Selection'] == 'Start-End-Date') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom|schedule-selection"
                            data-next-field="time-date-selection|locked-box"><?php _e('Start/End Date', 'if-so'); ?></option>
                       <option value="Schedule-Date" <?php echo ($rule['Time-Date-Schedule-Selection'] == 'Schedule-Date') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom|time-date-selection|time-date-pick-start-date|time-date-pick-end-date" data-next-field="schedule-selection|locked-box"><?php _e('Schedule', 'if-so'); ?></option>
                    </select>
                </div>


                <div class="form-group">

                    <div class="ab-testing-custom-sessions-display ifso-start-at-date <?php echo ($rule['trigger_type'] == 'Time-Date' && $rule['Time-Date-Schedule-Selection'] == "Schedule-Date") ? 'show-selection' : ''; ?>" data-field="schedule-selection">
                        <input type="hidden" class="schedule-input" name="repeater[<?php echo $current_version_index; ?>][Date-Time-Schedule]" value="" />
                        <p class="instruction"><i class="fa fa-info-circle" aria-hidden="true"></i> Press a column > drag > click again</p>
                        <div class="date-time-schedule" id="<?php echo "schedule-" . $current_version_index ?>"></div>
                        
                        <?php

                            if ($rule['trigger_type'] == 'Time-Date' &&
                                $rule['Time-Date-Schedule-Selection'] == "Schedule-Date") {
                                // Deserialize data
                                ?>

                                <script type="text/javascript">
                                    (function( $ ) {
                                            $(document).ready(function(){
                                                var scheduleData = JSON.parse('<?php _e($rule["Date-Time-Schedule"]); ?>');
                                                var scheduleId = '<?php _e("#schedule-".$current_version_index) ?>';

                                                $(scheduleId).data('artsy.dayScheduleSelector').deserialize(scheduleData);
                                            });
                                    })( jQuery );
                                </script>

                        <?php } ?>
                    
                    </div>


                </div>


                <div class="form-group">


                    <div class="ab-testing-custom-sessions-display ifso-start-at-date <?php echo (!empty($rule['Time-Date-Start']) && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>" data-field="time-date-selection">
                        <p class="start-end-date-headers"><?php _e('Start displaying content at:', 'if-so'); ?></p>
                    </div>


                    <select name="repeater[<?php echo $current_version_index; ?>][Time-Date-Start]" data-field="time-date-selection" class="form-control ab-testing ifso-pick-start-date <?php echo (!empty($rule['Time-Date-Start']) && $rule['trigger_type'] == 'Time-Date' &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>">
                        <option value="None" <?php echo ($rule['Time-Date-Start'] == 'None') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|time-date-pick-start-date"><?php _e('None', 'if-so'); ?></option>
                       <option value="PickADate" <?php echo ($rule['Time-Date-Start'] == 'PickADate' && $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom" data-next-field="time-date-pick-start-date|locked-box"><?php _e('From', 'if-so'); ?></option>
                    </select>
                </div>

                    <div class="form-group">
                        <div data-field="time-date-pick-start-date" class="pick-date-container <?php echo ($rule['Time-Date-Start'] == "PickADate" && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>" >
                            <input type="text" name="repeater[<?php echo $current_version_index; ?>][time-date-start-date]" data-field="time-date-pick-start-date" placeholder="<?php _e('Click to pick a Date', 'if-so'); ?>" class="form-control user-behavior-returning-custom datetimepickercustom-<?php echo $current_datetime_count; ?> datetimepicker <?php echo (!empty($rule['time-date-start-date']) && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['time-date-start-date']) && ($rule['trigger_type'] == 'Time-Date')) ? "value='{$rule['time-date-start-date']}'" : ''; ?> />
                        </div>
                    </div>

                    <div class="form-group">

                    <div class="ab-testing-custom-sessions-display ifso-end-at-date <?php echo (!empty($rule['Time-Date-End']) && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>" data-field="time-date-selection">
                        <p class="start-end-date-headers"><?php _e('Stop displaying content at:', 'if-so'); ?></p>
                    </div>


                    <select name="repeater[<?php echo $current_version_index; ?>][Time-Date-End]" data-field="time-date-selection" class="form-control ab-testing ifso-pick-end-date <?php echo (!empty($rule['Time-Date-End']) && $rule['trigger_type'] == 'Time-Date' &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>">
                        <option value="None" <?php echo ($rule['Time-Date-End'] == 'None') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|time-date-pick-end-date"><?php _e('None', 'if-so'); ?></option>
                       <option value="PickADate" <?php echo ($rule['Time-Date-End'] == 'PickADate' && $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|ab-testing-custom" data-next-field="time-date-pick-end-date|locked-box"><?php _e('Until', 'if-so'); ?></option>
                    </select>
                </div>


                    <div class="form-group">
                        <div data-field="time-date-pick-end-date" class="pick-date-container <?php echo ($rule['Time-Date-End'] == "PickADate" && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>">
                            <input type="text" name="repeater[<?php echo $current_version_index; ?>][time-date-end-date]" data-field="time-date-pick-end-date" placeholder="<?php _e('Click to pick a Date', 'if-so'); ?>" class="form-control user-behavior-returning-custom datetimepicker datetimepickercustom-<?php echo $current_datetime_count; ?> <?php echo (!empty($rule['time-date-end-date']) && ($rule['trigger_type'] == 'Time-Date') &&  $rule['Time-Date-Schedule-Selection'] == "Start-End-Date") ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['time-date-end-date']) && ($rule['trigger_type'] == 'Time-Date')) ? "value='{$rule['time-date-end-date']}'" : ''; ?>
                            />
                        </div>
                    </div>


                <div class="form-group">
                    <input type="text" name="repeater[<?php echo $current_version_index; ?>][ab-testing-custom-no-sessions]" data-field="ab-testing-custom" placeholder="<?php _e('Max no. of sessions', 'if-so'); ?>" class="form-control ab-testing-custom <?php echo (!empty($rule['ab-testing-custom-no-sessions']) && ($rule['trigger_type'] == 'AB-Testing')) ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['ab-testing-custom-no-sessions']) && ($rule['trigger_type'] == 'AB-Testing')) ? "value='{$rule['ab-testing-custom-no-sessions']}'" : ''; ?> />
                </div>



                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][User-Behavior]" data-field="user-behavior-selection" class="form-control ab-testing <?php echo (!empty($rule['User-Behavior']) && ($rule['trigger_type'] == 'User-Behavior')) ? 'show-selection' : ''; ?>">

                    <?php 
                    /*
                        <option value="LoggedIn" <?php echo ($rule['User-Behavior'] == 'LoggedIn') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|locked-box" ><?php _e('Logged In', 'if-so'); ?></option>
                        <option value="LoggedOut" <?php echo ($rule['User-Behavior'] == 'LoggedOut') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|locked-box" ><?php _e('Logged Out', 'if-so'); ?></option>
                    */
                    ?>
                        <option value="Logged" <?php echo ($rule['User-Behavior'] == 'Logged') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|locked-box" data-next-field="user-behavior-logged-selection" ><?php _e('Logged In', 'if-so'); ?></option>





                        <option value="NewUser" <?php echo ($rule['User-Behavior'] == 'NewUser') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|user-behavior-logged-selection" data-next-field="locked-box"><?php _e('New Visitor'.$displayClosedFeature, 'if-so'); ?></option>
                        <option value="Returning" <?php echo ($rule['User-Behavior'] == 'Returning') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-browser-language|user-behavior-device|user-behavior-logged-selection"
                         data-next-field="user-behavior-returning|locked-box"><?php _e('Returning Visitor'.$displayClosedFeature, 'if-so'); ?></option>

                        <option value="BrowserLanguage" <?php echo ($rule['User-Behavior'] == 'BrowserLanguage') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-returning|user-behavior-retn-custom|user-behavior-loggedinout|user-behavior-logged-selection"
                         data-next-field="user-behavior-browser-language|locked-box"><?php _e('Browser Language'.$displayClosedFeature, 'if-so'); ?></option>
                    </select>

                    <div class="form-group">

                            <div data-field="user-behavior-device" class="devices-container user-behavior-device <?php echo ($rule['trigger_type'] == 'Device') ? 'show-selection' : ''; ?>">

                                <div class="device-container">
                                <p class="deviceinstructions">Content will be displayed only on:</p>
                                    <input type="checkbox" name="repeater[<?php echo $current_version_index; ?>][user-behavior-device-mobile]"  class="form-control deviceformcontrol" <?php echo (!empty($rule['user-behavior-device-mobile']) && ($rule['trigger_type'] == 'Device') && $rule['user-behavior-device-mobile'] == "true") ? "checked" : ''; ?> />
                                    <span>Mobile</span>
                                </div>
                                <div class="device-container">
                                    <input type="checkbox" name="repeater[<?php echo $current_version_index; ?>][user-behavior-device-tablet]"  class="form-control deviceformcontrol" <?php echo (!empty($rule['user-behavior-device-tablet']) && ($rule['trigger_type'] == 'Device') && $rule['user-behavior-device-tablet'] == "true") ? "checked" : ''; ?> />
                                    <span>Tablet</span>
                                </div>
                                <div class="device-container">
                                    <input type="checkbox" name="repeater[<?php echo $current_version_index; ?>][user-behavior-device-desktop]"  class="form-control deviceformcontrol" <?php echo (!empty($rule['user-behavior-device-desktop']) && ($rule['trigger_type'] == 'Device') && $rule['user-behavior-device-desktop'] == "true") ? "checked" : ''; ?> />
                                    <span>Desktop</span>
                                </div>

                            </div>


                    </div>

                    <div class="form-group">

                        <select name="repeater[<?php echo $current_version_index; ?>][user-behavior-logged]" class="form-control referrer-custom <?php echo ($rule["trigger_type"] == "User-Behavior" && $rule['User-Behavior'] == "Logged") ? 'show-selection' : ''; ?>" data-field="user-behavior-logged-selection">

                            <option value="logged-in" <?php echo ($rule['user-behavior-logged'] == 'logged-in') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e('Yes', 'if-so'); ?></option>
                            <option value="logged-out" <?php echo ($rule['user-behavior-logged'] == 'logged-out') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e('No', 'if-so'); ?></option>


                        </select>

                    </div>

                    <div class="form-group">


                    <div class="ab-testing-custom-sessions-display <?php echo ($rule['User-Behavior'] == "Returning") ? 'show-selection' : ''; ?>" data-field="user-behavior-returning">
                        <p class="instructionabovefield"><?php _e('Show this content after:', 'if-so'); ?></p>
                    </div>

                        <select name="repeater[<?php echo $current_version_index; ?>][user-behavior-returning]" class="form-control referrer-custom <?php echo ($rule['User-Behavior'] == "Returning") ? 'show-selection' : ''; ?>" data-field="user-behavior-returning">

                            <option value="first-visit" <?php echo ($rule['user-behavior-returning'] == 'first-visit') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e('First Visit', 'if-so'); ?></option>
                            <option value="second-visit" <?php echo ($rule['user-behavior-returning'] == 'second-visit') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e('2 Visits', 'if-so'); ?></option>
                            <option value="three-visit" <?php echo ($rule['user-behavior-returning'] == 'three-visit') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e('3 Visits', 'if-so'); ?></option>
                            <option value="custom" <?php echo ($rule['user-behavior-returning'] == 'custom') ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"
                            data-next-field="user-behavior-retn-custom"><?php _e('Custom', 'if-so'); ?></option>

                        </select>




                        <div class="form-group" style="margin-top: 1em;">
                            <input type="text" name="repeater[<?php echo $current_version_index; ?>][user-behavior-retn-custom]" data-field="user-behavior-retn-custom" placeholder="<?php _e('Choose no. of visits', 'if-so'); ?>" class="form-control user-behavior-returning-custom <?php echo (!empty($rule['user-behavior-retn-custom']) && ($rule['trigger_type'] == 'User-Behavior')) ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['user-behavior-retn-custom']) && ($rule['trigger_type'] == 'User-Behavior')) ? "value='{$rule['user-behavior-retn-custom']}'" : ''; ?> />
                        </div>


                    </div>




                </div>
                
<!--                <input type="text" class="datetimepicker"> -->

                    <div class="form-group">

                        <select name="repeater[<?php echo $current_version_index; ?>][user-behavior-browser-language]" class="form-control referrer-custom <?php echo ($rule['User-Behavior'] == "BrowserLanguage") ? 'show-selection' : ''; ?>" data-field="user-behavior-browser-language">

                            <?php foreach ($languages as $language): ?>
                            
                                <option value="<?php echo $language[0]; ?>" <?php echo ($rule['user-behavior-browser-language'] == $language[0]) ? 'SELECTED' : ''; ?> data-reset="common-referrers|referrer-custom|url-custom|page-selection|user-behavior-retn-custom"><?php _e($language[4], 'if-so'); ?></option>

                            <?php endforeach; ?>
                        </select>

                    </div>

                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][page]" class="form-control referrer-custom <?php echo (!empty($rule['page'])) ? 'show-selection' : ''; ?>" data-field="page-selection">
                        <option value=""><?php _e('Choose a page', 'if-so'); ?></option>
                        <?php if(!empty($available_pages)): ?>
                            <?php foreach($available_pages as $available_page): ?>
                                <option value="<?php echo $available_page->ID; ?>" <?php echo ($rule['page'] == $available_page->ID) ? 'SELECTED' : ''; ?>><?php echo $available_page->post_title; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <input type="hidden" name="repeater[<?php echo $current_version_index; ?>][custom]" value="url" />
                <!-- to be added in the future for other types of custom referrers
                <!--<select name="repeater[<?php echo $current_version_index; ?>][custom]" class="form-control referrer-custom <?php echo (!empty($rule['custom'])) ? 'show-selection' : ''; ?>" data-field="referrer-custom">
                    <option value=""></option>
                    <option value="url" <?php echo ($rule['custom'] == 'url') ? 'SELECTED' : ''; ?>><?php _e('Url', 'if-so'); ?></option>
                </select>-->
                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][operator]" class="form-control referrer-custom url-custom <?php echo (!empty($rule['operator']) && $rule['trigger_type'] == 'referrer' && $rule['trigger'] == 'custom') ? 'show-selection' : ''; ?>" data-field="referrer-custom">
                        <!--<option value=""><?php _e('Choose an operator', 'if-so'); ?></option>-->
                        <option value="is" <?php echo ($rule['operator'] == 'is') ? 'SELECTED' : ''; ?>><?php _e('Url Is', 'if-so'); ?></option>
                        <option value="contains" <?php echo ($rule['operator'] == 'contains') ? 'SELECTED' : ''; ?>><?php _e('Url Contains', 'if-so'); ?></option>
                        <option value="is-not" <?php echo ($rule['operator'] == 'is-not') ? 'SELECTED' : ''; ?>><?php _e('Url Is Not', 'if-so'); ?></option>
                        <option value="not-containes" <?php echo ($rule['operator'] == 'not-containes') ? 'SELECTED' : ''; ?>><?php _e('Url Does Not Contain', 'if-so'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][advertising_platforms_option]" class="form-control advertising-platforms-option referrer-custom url-custom <?php echo (!empty($rule['advertising_platforms_option']) && $rule['trigger_type'] == 'advertising-platforms') ? 'show-selection' : ''; ?>" data-field="advertising-platforms-selection">
                        <option value="google" <?php echo ($rule['advertising_platforms_option'] == 'google') ? 'SELECTED' : ''; ?>><?php _e('Google Adwords', 'if-so'); ?></option>
                        <option value="facebook" <?php echo ($rule['advertising_platforms_option'] == 'facebook') ? 'SELECTED' : ''; ?>><?php _e('Facebook Ads', 'if-so'); ?></option>
                    </select>
                </div>

                <div class="form-group">

                    <input type="text" name="repeater[<?php echo $current_version_index; ?>][advertising_platforms]" data-field="advertising-platforms-selection" placeholder="<?php _e('Choose a name', 'if-so'); ?>" class="form-control url-custom <?php echo (!empty($rule['advertising_platforms']) && ($rule['trigger_type'] == 'advertising-platforms')) ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['advertising_platforms']) && ($rule['trigger_type'] == 'advertising-platforms')) ? "value='{$rule['advertising_platforms']}'" : ''; ?> />

                    <div class="instructions" style="<?php echo (!empty($rule['advertising_platforms']) && ($rule['trigger_type'] == 'advertising-platforms')) ? 'display:block;' : ''; ?>" data-field="advertising-platforms-selection">
                        <p><?php _e('Paste this string into the URL parameters field (inside Adwords):', 'if-so'); ?></p>
                        <pre><code><span class="platform-symbol"><?php echo (empty($rule['advertising_platforms_option']) || $rule['advertising_platforms_option'] == 'google') ? "{lpurl}?":""; ?></span>ifso=<b><?php echo ($rule['trigger_type'] == "advertising-platforms" && !empty($rule['advertising_platforms'])) ? $rule['advertising_platforms']:"the-name-you-choose"; ?></b></code></pre>
                        <p class="query-example"><?php _e('I.e., www.url.com?ifso=the-name-you-choose', 'if-so'); ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" name="repeater[<?php echo $current_version_index; ?>][compare_referrer]" data-field="referrer-custom" placeholder="<?php _e('http://your-referrer.com', 'if-so'); ?>" class="form-control referrer-custom <?php echo (!empty($rule['compare']) && ($rule['trigger_type'] == 'referrer')) ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['compare']) && ($rule['trigger_type'] == 'referrer')) ? "value='{$rule['compare']}'" : ''; ?> />
                    <input type="text" name="repeater[<?php echo $current_version_index; ?>][compare_url]" data-field="url-custom" placeholder="<?php _e('Name your query string', 'if-so'); ?>" class="form-control url-custom <?php echo (!empty($rule['compare']) && ($rule['trigger_type'] == 'url')) ? 'show-selection' : ''; ?>" <?php echo (!empty($rule['compare']) && ($rule['trigger_type'] == 'url')) ? "value='{$rule['compare']}'" : ''; ?> />
                    <?php /*if(!empty($rule['compare']) && ($rule['trigger_type'] == 'url')): ?>
                        <span class="" data-field="url-custom"><input type="text" onfocus="this.select();" readonly="readonly" value='<?php echo $rule['compare']; ?>' class="large-text code"></span>
                    <?php endif;*/ ?>
                    <div class="instructions" data-field="url-custom">
                        <p><?php _e('Add the following string at the end of your page URL to display the content:', 'if-so'); ?></p>
                        <pre><code>?ifso=<b>your-query-string</b></code></pre>
                        <p class="query-example"><?php _e('I.e., www.url.com?ifso=your-query-string', 'if-so'); ?></p>
                    </div>
                    <div class="custom-url-display instructions" <?php echo (!empty($rule['compare']) && ($rule['trigger_type'] == 'url')) ? 'style="display:block;"' : ''; ?> data-field="custom-url-display">
                        <p><?php _e('Add the following parameter at the end of the page URL', 'if-so'); ?>:</p>
                        <pre><code>?ifso=<b><?php echo (!empty($rule['compare'])) ? $rule['compare'] : ''; ?></b></code></pre>
                        <p class="query-example"><?php _e('I.e., www.url.com?ifso=your-query-string', 'if-so'); ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <select name="repeater[<?php echo $current_version_index; ?>][chosen-common-referrers]" class="form-control referrer-custom url-custom <?php echo ($rule['trigger'] == 'common-referrers' && !empty($rule['chosen-common-referrers'])) ? 'show-selection' : ''; ?>" data-field="common-referrers">
                        <option value="google" <?php echo ($rule['chosen-common-referrers'] == 'google') ? 'SELECTED' : ''; ?>><?php _e('Google', 'if-so'); ?></option>
                        <option value="facebook" <?php echo ($rule['chosen-common-referrers'] == 'facebook') ? 'SELECTED' : ''; ?>><?php _e('Facebook', 'if-so'); ?></option>
                    </select>
                </div>

            </div>

            <div class="col-md-9">
                <?php if($is_template): ?>
                    <div class="repeater-editor-wrap"></div>
                <?php else: ?>
                    <?php $version_content = (!empty($data_versions[$index])) ? $data_versions[$index] : ''; ?>
                    <?php wp_editor( $version_content, 'repeatable_editor_content'.$current_version_index, array(
                        'wpautop'       => true,
                        'textarea_name' => 'repeater['.$current_version_index.'][repeatable_editor_content]',
                        'textarea_rows' => 10,
                    ) ); ?>
                <?php endif; ?>
            </div>

            <!-- begin testing mode section -->

            <?php if ( (($is_template &&
                        !empty($data_rules) && isset($data_rules[0]['testing-mode'])) ||
                      (isset($rule['testing-mode']) &&
                      strval($rule['testing-mode']-2) != strval($current_version_index))) ||
                      empty($data_rules)): ?>

                <div class="col-md-1 ifso-tm-section">

                    <a href="#" onclick="return false;" title="Force to display the current version. Use this option to exemain how it fits in your site." class="tm-tip ifso_tooltip">?</a>
                    <span class="text">Testing Mode</span>
                    <div  class="ifso-tm circle"></div>

                </div>

            <?php else: ?>

                <div class="col-md-1 ifso-tm-section">

                    <a href="#" onclick="return false;" title="Force to display the current version. Use this option to exemain how it fits in your site." class="tm-tip">?</a>
                    <span class="text">Testing Mode</span>
                    <div  class="ifso-tm circle circle-active"></div>

                </div>

            <?php endif; ?>

            <!-- end testing mode section -->

        </div>

            <?php if ( ($is_template &&
                        !empty($data_rules) && isset($data_rules[0]['testing-mode']) &&
                        is_numeric($data_rules[0]['testing-mode'])) ||
                      (isset($rule['testing-mode']) &&
                        is_numeric($rule['testing-mode']) &&
                      strval($rule['testing-mode']-2) != strval($current_version_index))): ?>
                <div class="ifso-tm-overlay">
                    <span class="text">Testing Mode</span>
                </div>
        <?php endif; ?>

        <?php if ((isset($rule['freeze-mode']) &&
                  $rule['freeze-mode'] == "true")): ?>
            <div class="ifso-freeze-overlay ifso_tooltip" title="Inactive mode">
            </div>
        <?php endif; ?>

    </div>
<?php 
}
//echo "<pre>".print_r($data_rules, true)."</pre>";
?>

<?php if (!$isLicenseValid): ?>

<!-- <div class="get-license">
    Enter your license key to unlock the full power of If>So. Click here to get your free license key.
    <span class="get-license-btn">GET YOUR FREE LICENSE KEY<i class="fa fa-play" style="margin-left:10px;" aria-hidden="true"></i>
</span>
    
</div> -->

<!-- <div class="get-license clearfix">
    <div class="text">
        Enter your license key to unlock the full power of If&gt;So. Click here to get your free license key.
    </div>
    <a href="http://www.if-so.com/free-license/" class="get-license-btn" target="_blank" >GET YOUR FREE LICENSE KEY<i class="fa fa-play" style="margin-left:10px;" aria-hidden="true"></i>
    </a>
</div> -->

<?php endif; ?>

<div class="admin-trigger-wrap"><!--wrap-->
    <div id="repeater-template">
        <?php get_rule_item(null, array(), true); ?>
    </div>

    <!-- repeater -->
    <div class="repeater">
        <!--
            The value given to the data-repeater-list attribute will be used as the
            base of rewritten name attributes.  In this example, the first
            data-repeater-item's name attribute would become group-a[0][text-input],
            and the second data-repeater-item would become group-a[1][text-input]
        -->
        <?php // echo "<pre>".print_r($data_rules, true)."</pre>"; ?>
        <?php // echo "<pre>".print_r($data_versions, true)."</pre>"; ?>
        
        <?php if(!empty($data_rules)): ?>
            <?php foreach($data_rules as $index => $rule): ?>
                <?php get_rule_item($index, $rule); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <?php get_rule_item(0); ?>
        <?php endif; ?>

        <input type="hidden" id="tm-input" name="testing-mode" value="<?php echo (!empty($data_rules) && isset($data_rules[0]['testing-mode'])) ? $data_rules[0]['testing-mode'] : ''; ?>" />
        <button type="button" id="reapeater-add" class="btn-add"><i class="fa fa-plus highlighted" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?php _e('Add Another Version', 'if-so'); ?></button>
    </div>

    <div class="rule-item default-repeater-item">
        <div class="row rule-wrap default-rule-wrap">
            <div class="col-md-3">
                <h3>
                    <!--<span class="version-count">1</span>-->
                    <?php _e('Default Content', 'if-so'); ?>
                </h3>
                <?php if(false): /*$post_status != 'publish'):*/ ?>
                    <p class="initial-instructions"><b><?php _e('', 'if-so'); ?>1</b> <?php _e('Fill in default content', 'if-so'); ?></p>
                                    <?php endif; ?>
                <p><?php _e('Default content shows when none of the conditions are met.', 'if-so'); ?><br><br> <?php _e('Leave it blank if you', 'if-so'); ?> <strong><?php _e('do not', 'if-so'); ?></strong> <?php _e('want to display anything by default', 'if-so'); ?>.</p>
            </div>

            <div class="col-md-9">
                <?php 
                $default_value = (!empty($data_default)) ? $data_default : '';
                wp_editor( $default_value, 'trigger-default', array(
                    'wpautop'       => true,
                    'textarea_name' => 'ifso_default',
                    'textarea_rows' => 10,
                ));
                ?>
            </div>

           

        </div>
        
        <?php if (!empty($data_rules) && isset($data_rules[0]['testing-mode']) &&
                  is_numeric($data_rules[0]['testing-mode']) &&
                  strval($data_rules[0]['testing-mode']) != "0"): ?>
                <div class="ifso-tm-overlay">
                    <span class="text">Testing Mode</span>
                </div>
        <?php endif; ?>

    </div>

    <div class="container submit-bottom-wrap">
        <div class="row">
            <div class="col-xs-12">
                <?php $submit_text = ($post_status == 'publish') ? __('Update', 'if-so') : __('Publish', 'if-so'); ?>
                <?php if($post_status != 'publish'): ?>
                <p class="reminder">
                    <b class="highlighted"><?php _e('Done', 'if-so'); ?>?</b>
                    <?php _e('Press Publish and get a shortcode to paste in your website', 'if-so'); ?>
                    &nbsp;<!--<span class="submit-btn-wrap"><?php submit_button( $submit_text, 'primary', 'submit', false, NULL ); ?></span>-->
                </p>

                <?php endif; /*else: ?>
                <span class="submit-btn-wrap"><?php submit_button( $submit_text, 'primary', 'submit', false, NULL ); ?></span>
                <?php endif; */?>
            </div>
        </div>
    </div>

</div><!-- /.wrap -->