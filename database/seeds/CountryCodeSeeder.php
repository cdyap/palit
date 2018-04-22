<?php

use Illuminate\Database\Seeder;

class CountryCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('app_settings')->insert([
        	['name' => 'country_code', 'value' => '+213', 'value_2' => 'Algeria (+213)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+376', 'value_2' => 'Andorra (+376)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+244', 'value_2' => 'Angola (+244)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1264', 'value_2' => 'Anguilla (+1264)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1268', 'value_2' => 'Antigua &amp; Barbuda (+1268)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+54', 'value_2' => 'Argentina (+54)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+374', 'value_2' => 'Armenia (+374)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+297', 'value_2' => 'Aruba (+297)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+61', 'value_2' => 'Australia (+61)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+43', 'value_2' => 'Austria (+43)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+994', 'value_2' => 'Azerbaijan (+994)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1242', 'value_2' => 'Bahamas (+1242)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+973', 'value_2' => 'Bahrain (+973)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+880', 'value_2' => 'Bangladesh (+880)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1246', 'value_2' => 'Barbados (+1246)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+375', 'value_2' => 'Belarus (+375)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+32', 'value_2' => 'Belgium (+32)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+501', 'value_2' => 'Belize (+501)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+229', 'value_2' => 'Benin (+229)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1441', 'value_2' => 'Bermuda (+1441)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+975', 'value_2' => 'Bhutan (+975)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+591', 'value_2' => 'Bolivia (+591)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+387', 'value_2' => 'Bosnia Herzegovina (+387)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+267', 'value_2' => 'Botswana (+267)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+55', 'value_2' => 'Brazil (+55)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+673', 'value_2' => 'Brunei (+673)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+359', 'value_2' => 'Bulgaria (+359)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+226', 'value_2' => 'Burkina Faso (+226)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+257', 'value_2' => 'Burundi (+257)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+855', 'value_2' => 'Cambodia (+855)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+237', 'value_2' => 'Cameroon (+237)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1', 'value_2' => 'Canada (+1)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+238', 'value_2' => 'Cape Verde Islands (+238)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1345', 'value_2' => 'Cayman Islands (+1345)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+236', 'value_2' => 'Central African Republic (+236)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+56', 'value_2' => 'Chile (+56)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+86', 'value_2' => 'China (+86)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+57', 'value_2' => 'Colombia (+57)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+269', 'value_2' => 'Comoros (+269)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+242', 'value_2' => 'Congo (+242)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+682', 'value_2' => 'Cook Islands (+682)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+506', 'value_2' => 'Costa Rica (+506)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+385', 'value_2' => 'Croatia (+385)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+53', 'value_2' => 'Cuba (+53)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+90392', 'value_2' => 'Cyprus North (+90392)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+357', 'value_2' => 'Cyprus South (+357)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+42', 'value_2' => 'Czech Republic (+42)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+45', 'value_2' => 'Denmark (+45)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+253', 'value_2' => 'Djibouti (+253)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1809', 'value_2' => 'Dominica (+1809)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1809', 'value_2' => 'Dominican Republic (+1809)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+593', 'value_2' => 'Ecuador (+593)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+20', 'value_2' => 'Egypt (+20)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+503', 'value_2' => 'El Salvador (+503)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+240', 'value_2' => 'Equatorial Guinea (+240)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+291', 'value_2' => 'Eritrea (+291)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+372', 'value_2' => 'Estonia (+372)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+251', 'value_2' => 'Ethiopia (+251)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+500', 'value_2' => 'Falkland Islands (+500)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+298', 'value_2' => 'Faroe Islands (+298)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+679', 'value_2' => 'Fiji (+679)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+358', 'value_2' => 'Finland (+358)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+33', 'value_2' => 'France (+33)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+594', 'value_2' => 'French Guiana (+594)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+689', 'value_2' => 'French Polynesia (+689)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+241', 'value_2' => 'Gabon (+241)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+220', 'value_2' => 'Gambia (+220)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7880', 'value_2' => 'Georgia (+7880)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+49', 'value_2' => 'Germany (+49)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+233', 'value_2' => 'Ghana (+233)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+350', 'value_2' => 'Gibraltar (+350)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+30', 'value_2' => 'Greece (+30)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+299', 'value_2' => 'Greenland (+299)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1473', 'value_2' => 'Grenada (+1473)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+590', 'value_2' => 'Guadeloupe (+590)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+671', 'value_2' => 'Guam (+671)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+502', 'value_2' => 'Guatemala (+502)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+224', 'value_2' => 'Guinea (+224)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+245', 'value_2' => 'Guinea - Bissau (+245)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+592', 'value_2' => 'Guyana (+592)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+509', 'value_2' => 'Haiti (+509)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+504', 'value_2' => 'Honduras (+504)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+852', 'value_2' => 'Hong Kong (+852)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+36', 'value_2' => 'Hungary (+36)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+354', 'value_2' => 'Iceland (+354)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+91', 'value_2' => 'India (+91)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+62', 'value_2' => 'Indonesia (+62)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+98', 'value_2' => 'Iran (+98)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+964', 'value_2' => 'Iraq (+964)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+353', 'value_2' => 'Ireland (+353)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+972', 'value_2' => 'Israel (+972)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+39', 'value_2' => 'Italy (+39)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1876', 'value_2' => 'Jamaica (+1876)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+81', 'value_2' => 'Japan (+81)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+962', 'value_2' => 'Jordan (+962)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7', 'value_2' => 'Kazakhstan (+7)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+254', 'value_2' => 'Kenya (+254)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+686', 'value_2' => 'Kiribati (+686)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+850', 'value_2' => 'Korea North (+850)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+82', 'value_2' => 'Korea South (+82)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+965', 'value_2' => 'Kuwait (+965)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+996', 'value_2' => 'Kyrgyzstan (+996)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+856', 'value_2' => 'Laos (+856)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+371', 'value_2' => 'Latvia (+371)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+961', 'value_2' => 'Lebanon (+961)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+266', 'value_2' => 'Lesotho (+266)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+231', 'value_2' => 'Liberia (+231)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+218', 'value_2' => 'Libya (+218)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+417', 'value_2' => 'Liechtenstein (+417)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+370', 'value_2' => 'Lithuania (+370)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+352', 'value_2' => 'Luxembourg (+352)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+853', 'value_2' => 'Macao (+853)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+389', 'value_2' => 'Macedonia (+389)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+261', 'value_2' => 'Madagascar (+261)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+265', 'value_2' => 'Malawi (+265)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+60', 'value_2' => 'Malaysia (+60)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+960', 'value_2' => 'Maldives (+960)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+223', 'value_2' => 'Mali (+223)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+356', 'value_2' => 'Malta (+356)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+692', 'value_2' => 'Marshall Islands (+692)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+596', 'value_2' => 'Martinique (+596)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+222', 'value_2' => 'Mauritania (+222)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+269', 'value_2' => 'Mayotte (+269)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+52', 'value_2' => 'Mexico (+52)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+691', 'value_2' => 'Micronesia (+691)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+373', 'value_2' => 'Moldova (+373)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+377', 'value_2' => 'Monaco (+377)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+976', 'value_2' => 'Mongolia (+976)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1664', 'value_2' => 'Montserrat (+1664)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+212', 'value_2' => 'Morocco (+212)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+258', 'value_2' => 'Mozambique (+258)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+95', 'value_2' => 'Myanmar (+95)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+264', 'value_2' => 'Namibia (+264)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+674', 'value_2' => 'Nauru (+674)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+977', 'value_2' => 'Nepal (+977)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+31', 'value_2' => 'Netherlands (+31)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+687', 'value_2' => 'New Caledonia (+687)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+64', 'value_2' => 'New Zealand (+64)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+505', 'value_2' => 'Nicaragua (+505)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+227', 'value_2' => 'Niger (+227)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+234', 'value_2' => 'Nigeria (+234)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+683', 'value_2' => 'Niue (+683)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+672', 'value_2' => 'Norfolk Islands (+672)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+670', 'value_2' => 'Northern Marianas (+670)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+47', 'value_2' => 'Norway (+47)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+968', 'value_2' => 'Oman (+968)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+680', 'value_2' => 'Palau (+680)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+507', 'value_2' => 'Panama (+507)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+675', 'value_2' => 'Papua New Guinea (+675)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+595', 'value_2' => 'Paraguay (+595)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+51', 'value_2' => 'Peru (+51)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+48', 'value_2' => 'Poland (+48)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+63', 'value_2' => 'Philippines (+63)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+351', 'value_2' => 'Portugal (+351)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1787', 'value_2' => 'Puerto Rico (+1787)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+974', 'value_2' => 'Qatar (+974)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+262', 'value_2' => 'Reunion (+262)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+40', 'value_2' => 'Romania (+40)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7', 'value_2' => 'Russia (+7)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+250', 'value_2' => 'Rwanda (+250)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+378', 'value_2' => 'San Marino (+378)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+239', 'value_2' => 'Sao Tome &amp; Principe (+239)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+966', 'value_2' => 'Saudi Arabia (+966)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+221', 'value_2' => 'Senegal (+221)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+381', 'value_2' => 'Serbia (+381)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+248', 'value_2' => 'Seychelles (+248)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+232', 'value_2' => 'Sierra Leone (+232)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+65', 'value_2' => 'Singapore (+65)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+421', 'value_2' => 'Slovak Republic (+421)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+386', 'value_2' => 'Slovenia (+386)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+677', 'value_2' => 'Solomon Islands (+677)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+252', 'value_2' => 'Somalia (+252)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+27', 'value_2' => 'South Africa (+27)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+34', 'value_2' => 'Spain (+34)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+94', 'value_2' => 'Sri Lanka (+94)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+290', 'value_2' => 'St. Helena (+290)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1869', 'value_2' => 'St. Kitts (+1869)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1758', 'value_2' => 'St. Lucia (+1758)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+249', 'value_2' => 'Sudan (+249)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+597', 'value_2' => 'Suriname (+597)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+268', 'value_2' => 'Swaziland (+268)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+46', 'value_2' => 'Sweden (+46)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+41', 'value_2' => 'Switzerland (+41)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+963', 'value_2' => 'Syria (+963)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+886', 'value_2' => 'Taiwan (+886)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7', 'value_2' => 'Tajikstan (+7)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+66', 'value_2' => 'Thailand (+66)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+228', 'value_2' => 'Togo (+228)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+676', 'value_2' => 'Tonga (+676)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1868', 'value_2' => 'Trinidad &amp; Tobago (+1868)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+216', 'value_2' => 'Tunisia (+216)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+90', 'value_2' => 'Turkey (+90)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7', 'value_2' => 'Turkmenistan (+7)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+993', 'value_2' => 'Turkmenistan (+993)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1649', 'value_2' => 'Turks &amp; Caicos Islands (+1649)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+688', 'value_2' => 'Tuvalu (+688)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+256', 'value_2' => 'Uganda (+256)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+44', 'value_2' => 'UK (+44)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+380', 'value_2' => 'Ukraine (+380)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+971', 'value_2' => 'United Arab Emirates (+971)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+598', 'value_2' => 'Uruguay (+598)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+1', 'value_2' => 'USA (+1)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+7', 'value_2' => 'Uzbekistan (+7)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+678', 'value_2' => 'Vanuatu (+678)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+379', 'value_2' => 'Vatican City (+379)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+58', 'value_2' => 'Venezuela (+58)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+84', 'value_2' => 'Vietnam (+84)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+84', 'value_2' => 'Virgin Islands - British (+1284)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+84', 'value_2' => 'Virgin Islands - US (+1340)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+681', 'value_2' => 'Wallis &amp; Futuna (+681)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+969', 'value_2' => 'Yemen (North)(+969)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+967', 'value_2' => 'Yemen (South)(+967)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+260', 'value_2' => 'Zambia (+260)', 'value_3' => null],
			['name' => 'country_code', 'value' => '+263', 'value_2' => 'Zimbabwe (+263)', 'value_3' => null],

        ]);
    }
}

