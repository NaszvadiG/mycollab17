<?php

  /**
   * Select language locale helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select locale box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_locale($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', false, true);
    $optional = array_var($params, 'optional', false, true);
    
    $locales = array(
      'af.UTF-8' => 'Afrikaans',
      'af_ZA.UTF-8' => 'Afrikaans - South Africa',
      'sq.UTF-8' => 'Albanian',
      'sq_AL.UTF-8' => 'Albanian - Albania',
      'ar.UTF-8' => 'Arabic',
      'ar_DZ.UTF-8' => 'Arabic - Algeria',
      'ar_BH.UTF-8' => 'Arabic - Bahrain',
      'ar_EG.UTF-8' => 'Arabic - Egypt',
      'ar_IQ.UTF-8' => 'Arabic - Iraq',
      'ar_JO.UTF-8' => 'Arabic - Jordan',
      'ar_KW.UTF-8' => 'Arabic - Kuwait',
      'ar_LB.UTF-8' => 'Arabic - Lebanon',
      'ar_LY.UTF-8' => 'Arabic - Libya',
      'ar_MA.UTF-8' => 'Arabic - Morocco',
      'ar_OM.UTF-8' => 'Arabic - Oman',
      'ar_QA.UTF-8' => 'Arabic - Qatar',
      'ar_SA.UTF-8' => 'Arabic - Saudi Arabia',
      'ar_SY.UTF-8' => 'Arabic - Syria',
      'ar_TN.UTF-8' => 'Arabic - Tunisia',
      'ar_AE.UTF-8' => 'Arabic - United Arab Emirates',
      'ar_YE.UTF-8' => 'Arabic - Yemen',
      'hy.UTF-8' => 'Armenian',
      'hy_AM.UTF-8' => 'Armenian - Armenia',
      'az.UTF-8' => 'Azeri',
      'az_AZ_Cyrl.UTF-8' => 'Azeri (Cyrillic) - Azerbaijan',
      'az_AZ_Latn.UTF-8' => 'Azeri (Latin) - Azerbaijan',
      'eu.UTF-8' => 'Basque',
      'eu_ES.UTF-8' => 'Basque - Basque',
      'be.UTF-8' => 'Belarusian',
      'be_BY.UTF-8' => 'Belarusian - Belarus',
      'bg.UTF-8' => 'Bulgarian',
      'bg_BG.UTF-8' => 'Bulgarian - Bulgaria',
      'ca.UTF-8' => 'Catalan',
      'ca_ES.UTF-8' => 'Catalan - Catalan',
      'zh_HK.UTF-8' => 'Chinese - Hong Kong SAR',
      'zh_MO.UTF-8' => 'Chinese - Macao SAR',
      'zh_CN.UTF-8' => 'Chinese - China',
      'zh_CHS.UTF-8' => 'Chinese (Simplified)',
      'zh_SG.UTF-8' => 'Chinese - Singapore',
      'zh_TW.UTF-8' => 'Chinese - Taiwan',
      'zh_CHT.UTF-8' => 'Chinese (Traditional)',
      'hr.UTF-8' => 'Croatian',
      'hr_HR.UTF-8' => 'Croatian - Croatia',
      'cs.UTF-8' => 'Czech',
      'cs_CZ.UTF-8' => 'Czech - Czech Republic',
      'da.UTF-8' => 'Danish',
      'da_DK.UTF-8' => 'Danish - Denmark',
      'div.UTF-8' => 'Dhivehi',
      'div_MV.UTF-8' => 'Dhivehi - Maldives',
      'nl.UTF-8' => 'Dutch',
      'nl_BE.UTF-8' => 'Dutch - Belgium',
      'nl_NL.UTF-8' => 'Dutch - The Netherlands',
      'en.UTF-8' => 'English',
      'en_AU.UTF-8' => 'English - Australia',
      'en_BZ.UTF-8' => 'English - Belize',
      'en_CA.UTF-8' => 'English - Canada',
      'en_CB.UTF-8' => 'English - Caribbean',
      'en_IE.UTF-8' => 'English - Ireland',
      'en_JM.UTF-8' => 'English - Jamaica',
      'en_NZ.UTF-8' => 'English - New Zealand',
      'en_PH.UTF-8' => 'English - Philippines',
      'en_ZA.UTF-8' => 'English - South Africa',
      'en_TT.UTF-8' => 'English - Trinidad and Tobago',
      'en_GB.UTF-8' => 'English - United Kingdom',
      'en_US.UTF-8' => 'English - United States',
      'en_ZW.UTF-8' => 'English - Zimbabwe',
      'et.UTF-8' => 'Estonian',
      'et_EE.UTF-8' => 'Estonian - Estonia',
      'fo.UTF-8' => 'Faroese',
      'fo_FO.UTF-8' => 'Faroese - Faroe Islands',
      'fa.UTF-8' => 'Farsi',
      'fa_IR.UTF-8' => 'Farsi - Iran',
      'fi.UTF-8' => 'Finnish',
      'fi_FI.UTF-8' => 'Finnish - Finland',
      'fr.UTF-8' => 'French',
      'fr_BE.UTF-8' => 'French - Belgium',
      'fr_CA.UTF-8' => 'French - Canada',
      'fr_FR.UTF-8' => 'French - France',
      'fr_LU.UTF-8' => 'French - Luxembourg',
      'fr_MC.UTF-8' => 'French - Monaco',
      'fr_CH.UTF-8' => 'French - Switzerland',
      'gl.UTF-8' => 'Galician',
      'gl_ES.UTF-8' => 'Galician - Galician',
      'ka.UTF-8' => 'Georgian',
      'ka_GE.UTF-8' => 'Georgian - Georgia',
      'de.UTF-8' => 'German',
      'de_AT.UTF-8' => 'German - Austria',
      'de_DE.UTF-8' => 'German - Germany',
      'de_LI.UTF-8' => 'German - Liechtenstein',
      'de_LU.UTF-8' => 'German - Luxembourg',
      'de_CH.UTF-8' => 'German - Switzerland',
      'el.UTF-8' => 'Greek',
      'el_GR.UTF-8' => 'Greek - Greece',
      'gu.UTF-8' => 'Gujarati',
      'gu_IN.UTF-8' => 'Gujarati - India',
      'he.UTF-8' => 'Hebrew',
      'he_IL.UTF-8' => 'Hebrew - Israel',
      'hi.UTF-8' => 'Hindi',
      'hi_IN.UTF-8' => 'Hindi - India',
      'hu.UTF-8' => 'Hungarian',
      'hu_HU.UTF-8' => 'Hungarian - Hungary',
      'is.UTF-8' => 'Icelandic',
      'is_IS.UTF-8' => 'Icelandic - Iceland',
      'id.UTF-8' => 'Indonesian',
      'id_ID.UTF-8' => 'Indonesian - Indonesia',
      'it.UTF-8' => 'Italian',
      'it_IT.UTF-8' => 'Italian - Italy',
      'it_CH.UTF-8' => 'Italian - Switzerland',
      'ja.UTF-8' => 'Japanese',
      'ja_JP.UTF-8' => 'Japanese - Japan',
      'kn.UTF-8' => 'Kannada',
      'kn_IN.UTF-8' => 'Kannada - India',
      'kk.UTF-8' => 'Kazakh',
      'kk_KZ.UTF-8' => 'Kazakh - Kazakhstan',
      'kok.UTF-8' => 'Konkani',
      'kok_IN.UTF-8' => 'Konkani - India',
      'ko.UTF-8' => 'Korean',
      'ko_KR.UTF-8' => 'Korean - Korea',
      'ky.UTF-8' => 'Kyrgyz',
      'ky_KG.UTF-8' => 'Kyrgyz - Kyrgyzstan',
      'lv.UTF-8' => 'Latvian',
      'lv_LV.UTF-8' => 'Latvian - Latvia',
      'lt.UTF-8' => 'Lithuanian',
      'lt_LT.UTF-8' => 'Lithuanian - Lithuania',
      'mk.UTF-8' => 'Macedonian',
      'mk_MK.UTF-8' => 'Macedonian - Former Yugoslav Republic of Macedonia',
      'ms.UTF-8' => 'Malay',
      'ms_BN.UTF-8' => 'Malay - Brunei',
      'ms_MY.UTF-8' => 'Malay - Malaysia',
      'mr.UTF-8' => 'Marathi',
      'mr_IN.UTF-8' => 'Marathi - India',
      'mn.UTF-8' => 'Mongolian',
      'mn_MN.UTF-8' => 'Mongolian - Mongolia',
      'no.UTF-8' => 'Norwegian',
      'nb_NO.UTF-8' => 'Norwegian (Bokm�l) - Norway',
      'nn_NO.UTF-8' => 'Norwegian (Nynorsk) - Norway',
      'pl.UTF-8' => 'Polish',
      'pl_PL.UTF-8' => 'Polish - Poland',
      'pt.UTF-8' => 'Portuguese',
      'pt_BR.UTF-8' => 'Portuguese - Brazil',
      'pt_PT.UTF-8' => 'Portuguese - Portugal',
      'pa.UTF-8' => 'Punjabi',
      'pa_IN.UTF-8' => 'Punjabi - India',
      'ro.UTF-8' => 'Romanian',
      'ro_RO.UTF-8' => 'Romanian - Romania',
      'ru.UTF-8' => 'Russian',
      'ru_RU.UTF-8' => 'Russian - Russia',
      'sa.UTF-8' => 'Sanskrit',
      'sa_IN.UTF-8' => 'Sanskrit - India',
      'sr_SP_Cyrl.UTF-8' => 'Serbian (Cyrillic) - Serbia',
      'sr_SP_Latn.UTF-8' => 'Serbian (Latin) - Serbia',
      'sk.UTF-8' => 'Slovak',
      'sk_SK.UTF-8' => 'Slovak - Slovakia',
      'sl.UTF-8' => 'Slovenian',
      'sl_SI.UTF-8' => 'Slovenian - Slovenia',
      'es.UTF-8' => 'Spanish',
      'es_AR.UTF-8' => 'Spanish - Argentina',
      'es_BO.UTF-8' => 'Spanish - Bolivia',
      'es_CL.UTF-8' => 'Spanish - Chile',
      'es_CO.UTF-8' => 'Spanish - Colombia',
      'es_CR.UTF-8' => 'Spanish - Costa Rica',
      'es_DO.UTF-8' => 'Spanish - Dominican Republic',
      'es_EC.UTF-8' => 'Spanish - Ecuador',
      'es_SV.UTF-8' => 'Spanish - El Salvador',
      'es_GT.UTF-8' => 'Spanish - Guatemala',
      'es_HN.UTF-8' => 'Spanish - Honduras',
      'es_MX.UTF-8' => 'Spanish - Mexico',
      'es_NI.UTF-8' => 'Spanish - Nicaragua',
      'es_PA.UTF-8' => 'Spanish - Panama',
      'es_PY.UTF-8' => 'Spanish - Paraguay',
      'es_PE.UTF-8' => 'Spanish - Peru',
      'es_PR.UTF-8' => 'Spanish - Puerto Rico',
      'es_ES.UTF-8' => 'Spanish - Spain',
      'es_UY.UTF-8' => 'Spanish - Uruguay',
      'es_VE.UTF-8' => 'Spanish - Venezuela',
      'sw.UTF-8' => 'Swahili',
      'sw_KE.UTF-8' => 'Swahili - Kenya',
      'sv.UTF-8' => 'Swedish',
      'sv_FI.UTF-8' => 'Swedish - Finland',
      'sv_SE.UTF-8' => 'Swedish - Sweden',
      'syr.UTF-8' => 'Syriac',
      'syr_SY.UTF-8' => 'Syriac - Syria',
      'ta.UTF-8' => 'Tamil',
      'ta_IN.UTF-8' => 'Tamil - India',
      'tt.UTF-8' => 'Tatar',
      'tt_RU.UTF-8' => 'Tatar - Russia',
      'te.UTF-8' => 'Telugu',
      'te_IN.UTF-8' => 'Telugu - India',
      'th.UTF-8' => 'Thai',
      'th_TH.UTF-8' => 'Thai - Thailand',
      'tr.UTF-8' => 'Turkish',
      'tr_TR.UTF-8' => 'Turkish - Turkey',
      'uk.UTF-8' => 'Ukrainian',
      'uk_UA.UTF-8' => 'Ukrainian - Ukraine',
      'ur.UTF-8' => 'Urdu',
      'ur_PK.UTF-8' => 'Urdu - Pakistan',
      'uz.UTF-8' => 'Uzbek',
      'uz_UZ_Cyrl.UTF-8' => 'Uzbek (Cyrillic) - Uzbekistan',
      'uz_UZ_Latn.UTF-8' => 'Uzbek (Latin) - Uzbekistan',
      'vi.UTF-8' => 'Vietnamese'
      
    );
  
    return HTML::selectFromPossibilities($name, $locales, $value, $params);
  } // smarty_function_select_locale