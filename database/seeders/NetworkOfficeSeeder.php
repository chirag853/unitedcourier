<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NetworkOffice;

class NetworkOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // India Offices
        $indiaOffices = [
            [
                'name' => 'New Delhi (Head Office)',
                'type' => 'india',
                'address' => 'United Worldwide Couriers Pvt Ltd Building No. 1, Bypass Road Mahipalpur New Delhi - 110037',
                'telephone' => '+91-11-46022222',
                'mobile' => '9810344167, 9810845610',
                'fax' => '+91-11-26783783',
                'email' => 'users@uwd.com',
                'sort_order' => 1,
            ],
            [
                'name' => 'Agra Branch',
                'type' => 'india',
                'address' => 'Shop No 01, Block No 13 (Cloth Market), Sanjay Palace, Agra - 282002',
                'telephone' => '+91 0562-4000868',
                'mobile' => '+91 9997361415',
                'contact_person' => 'Mr. Vijay Baghel',
                'email' => 'uwd.agra@yahoo.co.in',
                'sort_order' => 2,
            ],
            [
                'name' => 'Bangalore Branch',
                'type' => 'india',
                'address' => 'Bangalore - 560027 Karnataka India',
                'telephone' => '+91 99110 10844',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Ramesh Gothwal',
                'email' => 'gothwalramesh@yahoo.com',
                'sort_order' => 3,
            ],
            [
                'name' => 'Delhi Branch',
                'type' => 'india',
                'address' => 'B-3/9, Main Vikas Marg, Shakarpur Delhi - 110092',
                'telephone' => '011-22469800, 22469900',
                'mobile' => '+91 9811004409',
                'fax' => null,
                'contact_person' => 'Mr. Parveen',
                'email' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'Gurgaon Branch',
                'type' => 'india',
                'address' => 'Shankar Chowk Road, Near IOB, Dundahera, Gurgaon, 122016',
                'telephone' => '0124-2398467',
                'mobile' => '+91 98110 11907',
                'contact_person' => 'Mr. Nitin Dara',
                'email' => 'uwd@rediffmail.com',
                'sort_order' => 5,
            ],
            [
                'name' => 'Hyderabad Branch',
                'type' => 'india',
                'address' => 'Hyderabad Telangana 500026 India',
                'telephone' => null,
                'mobile' => '+91 99110 10844',
                'fax' => null,
                'contact_person' => 'Mr. Ramesh Gothwal',
                'email' => 'gothwalramesh@yahoo.com',
                'sort_order' => 6,
            ],
            [
                'name' => 'Jaipur Branch',
                'type' => 'india',
                'address' => '88, Nalanda Vihar, Maharani Farm Durgapura, Jaipur - 302018',
                'telephone' => '+91-141-2761852',
                'mobile' => '9828011007',
                'contact_person' => 'Mr. Yogesh Kapur',
                'email' => 'uwd@datainfosys.net',
                'sort_order' => 7,
            ],
            [
                'name' => 'Jalandhar Branch',
                'type' => 'india',
                'address' => 'Shop No. 27, Street No. 14, Dayal Nagar, Jalandhar - 144001',
                'telephone' => '0181-4620029',
                'mobile' => '9988429337',
                'fax' => null,
                'contact_person' => 'Mr. Vikas Patyal',
                'email' => null,
                'sort_order' => 8,
            ],
            [
                'name' => 'Jodhpur Branch',
                'type' => 'india',
                'address' => 'Jodhpur - 342003 (Rajasthan)',
                'telephone' => null,
                'mobile' => '+91 99110 10844',
                'fax' => null,
                'contact_person' => 'Mr. Ramesh Gothwal',
                'email' => 'gothwalramesh@yahoo.com',
                'sort_order' => 9,
            ],
            [
                'name' => 'Kolkata Branch',
                'type' => 'india',
                'address' => 'P-109 C.I.T Road Near Holy Child School Kolkata - 700014',
                'telephone' => '+91-33 40670000',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Manohar',
                'email' => 'uwdkolkata@gmail.com',
                'sort_order' => 10,
            ],
            [
                'name' => 'Ludhiana Branch',
                'type' => 'india',
                'address' => '717-C Model Town, 2nd Floor Gugri Road, Near Grand Mariane Hotel, Ludhiana 141002',
                'telephone' => null,
                'mobile' => '+91 9915631046, 9811004409',
                'fax' => null,
                'contact_person' => 'Mr. Harpal Saini',
                'email' => 'unitedworldwideludhiana@gmail.com',
                'sort_order' => 11,
            ],
            [
                'name' => 'Moradabad Branch',
                'type' => 'india',
                'address' => 'Shop # 3, 1st Floor Cloth Mkt., Opp Hotel Bhawan Budh Bazar Moradabad (U.P) 244001',
                'telephone' => '+91-591 2310570',
                'mobile' => '+91 9837064888',
                'contact_person' => 'Mr. Sunil Bhatia',
                'email' => 'br-mbd@rediffmail.com',
                'sort_order' => 12,
            ],
            [
                'name' => 'Mumbai Branch',
                'type' => 'india',
                'address' => 'Flat No A 103, 1st Floor Alankar Society N.M.P, Goregaon East Mumbai 400063',
                'telephone' => '+91-22-28419100',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Rahul Sharma',
                'email' => 'uwdmumbai@gmail.com',
                'sort_order' => 13,
            ],
            [
                'name' => 'Panipat Branch',
                'type' => 'india',
                'address' => '859/8 (Basement) Opp. Naval Cinema, Near Kotak Mahindra Bank G.T. Road Panipat 132103',
                'telephone' => '0180-4014168, 4015168',
                'mobile' => '+91 93131 72550',
                'contact_person' => 'Mr. Ramesh Saini',
                'email' => 'unitedpnp@gmail.com',
                'sort_order' => 14,
            ],
            [
                'name' => 'Chennai Branch',
                'type' => 'india',
                'address' => 'Gemini Parsn Commercial Complex, Basement, Nungambakkam, Chennai 600006',
                'telephone' => '+91 4448576317',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Manohar Verma',
                'email' => 'uwchennai@unitedcouriers.biz',
                'sort_order' => 15,
            ],
        ];

        // Overseas Offices
        $overseasOffices = [
            [
                'name' => 'New York (USA)',
                'type' => 'overseas',
                'address' => '218 West, 37th Street, 6th Floor, New York - 10018',
                'telephone' => '001-646-674-1750',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Sandeep Kapur',
                'email' => 'uwdny@uwd.com',
                'sort_order' => 1,
            ],
            [
                'name' => 'France',
                'type' => 'overseas',
                'address' => '14 Avenue Edouard Vaillant 93500 Pantin, France',
                'telephone' => '+33-1-56960102',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Albane',
                'email' => 'customerservice@allianceservice.com',
                'sort_order' => 2,
            ],
            [
                'name' => 'Germany',
                'type' => 'overseas',
                'address' => 'Kamm An Der Lache 71, 65474 Mainz-Germany',
                'telephone' => '0049-6142-836-9930',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Ginny Jasveer Singh',
                'email' => 'uwd@unitramworldwide.de',
                'sort_order' => 3,
            ],
            [
                'name' => 'Hong Kong',
                'type' => 'overseas',
                'address' => 'Unit 801-801A, Yeung Yiu Chung (No.8) Industrial Bldg, Kowloon Bay',
                'telephone' => '00 852 3580 0000',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Ms. Gloria / Heather',
                'email' => 'gloria@dreamsexpress.com',
                'sort_order' => 4,
            ],
            [
                'name' => 'Nepal',
                'type' => 'overseas',
                'address' => 'Kathmandu Municipality Ward No. 31 Airport Road, Kathmandu, Nepal',
                'telephone' => '+977-1-4470441',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Narendra Khatiwada',
                'email' => 'linkageexp@gmail.com',
                'sort_order' => 5,
            ],
            [
                'name' => 'United Kingdom',
                'type' => 'overseas',
                'address' => 'Unit 147B, S. Poyle Trading Est. Poyle Road, Colnbrook, Slough, SL3 0AA',
                'telephone' => '01753 676840',
                'mobile' => null,
                'fax' => null,
                'contact_person' => 'Mr. Ravi Soni',
                'email' => 'sales@uwd.uk.com',
                'sort_order' => 6,
            ],
        ];

        // Insert all offices one by one to avoid column order issues
        foreach (array_merge($indiaOffices, $overseasOffices) as $office) {
            NetworkOffice::create($office);
        }
    }
}
