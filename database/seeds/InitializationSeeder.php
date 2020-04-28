<?php

use App\AppDefault;
use Illuminate\Database\Seeder;

class InitializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $online_chat = [
            'time'  => '9am - 11pm',
            'url'   => 'https://www.online_chat.com',
        ];
        
        $appDefaults = [
            'OTP_expiry'      =>  5,
            'FAQ_link'        =>  'https://www.faq.com',
            'online_chat'     =>  json_encode($online_chat),
            'hotline_contact' =>  '+97123456780',
            'company_email'   =>  '+97123456780',
            'company_logo'    =>  'company_logo.png',
            'app_rows'        =>  8,
            'sys_rows'        =>  8
        ];

        AppDefault::create($appDefaults);
    }
}
