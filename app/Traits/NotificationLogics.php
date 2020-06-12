<?php

namespace App\Traits;

use App\DeviceToken;
use App\Mail\notifyMail;
use App\Order;
use App\User;
use Mail;
use App\Http\Resources\Api\Admin\Order as OrderResource;

trait NotificationLogics
{

    /**
    * Send Welcome Email to Customer
    */
    public static function notifyNewRegistration($customer_id)
    {  
        $customer = User::find($customer_id);
        
        $customerMailData = [
            'emailType' => 'new_registration',
            'name'      => $customer->full_name,
            'email'     => $customer->email,
            'subject'   => "BLACK-BLADGE: Welcome ".$customer->full_name,
            'message'   => "Welcome to BLACK-BLADGE..",
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));
        
        return true;
    }
       
    /**
    * TESTING NEW WAY TO REDUCE CODE REPEATITION
    */
    public static function notifyME($order, $notifyType)
    {  
       

        $notifyAdmin = false;
        $notifyCustomer = false;
        $notifyDriver = false;

        if($notifyType == 'new_booking'){
            $customerMessage = "Booking ID: #".$order->id. ". We will contact you soon.";
            $adminMessage = $order->customer->full_name. ' placed a new booking order #'.$order->id;
            $notifyAdmin = true;
            $notifyCustomer = true;
            $notifyDriver = true;
        }
        

        

        

        $notifyAdmin = [
            'notifyType' => $notifyType,
            'message'    => $adminMessage,
            'model'      => 'order',
            'url'        => $order->id
        ];

        $customer = User::find($order->customer_id);

        $notifyCustomer = [
            'notifyType' => $notifyType,
            'message'    => $customerMessage,
            'model'      => 'order',
            'url'        => $order->id
        ];

        // $customerMailData = [
        //     'emailType' => 'new_booking',
        //     'name'      => $customer->full_name,
        //     'email'     => $customer->email,
        //     'orderID'   => $order->id,
        //     'subject'   => "BLACK-BLADGE: Your Order: #".$order_id. " has been placed",
        //     'message'   => "We've received your New Order: #".$order_id. ". We will contact you soon.",
        // ];
        
        // Notify Customer in email
        // Mail::send(new notifyMail($customerMailData));

        // Send Notification to All Superadmins
        
        if($notifyAdmin){
            $superAdmin_ids = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'superAdmin');
                        })
                        ->pluck('id')
                        ->toArray();
            foreach($superAdmin_ids as $id){
                User::find($id)->pushNotification($notification);
            }
        }
        

        // Send Notification to All Drivers of that particular area
        foreach($driver_ids as $id){
            User::find($id)->AppNotification($notification);
        }
        
        $customer->AppNotification($notifyCustomer); 
        
        return true;
    }
       
    public static function notifyApp($order, $notifyType, $notifyID, $message, $sendTo)
    {
        $notification = [
            'notifyType' => $notifyType,
            'message'    => $message,
            'model'      => 'order',
            'url'        => $order->id
        ];

        if($sendTo=='web'){
            // Send Order Accepted Notification to Web Admins    
            User::find($notifyID)->pushNotification($notification); 
        }
        else{
            // Send Order Accepted Notification to Customer    
            User::find($notifyID)->AppNotification($notification); 
        }   
    }

    public static function getUserRoleIDs($type)
    {
        $IDs = User::whereHas('roles', function ($query) use ($type) {
                        $query->where('name', '=', $type);
                    })
                    ->pluck('id')
                    ->toArray();

        return $IDs;
    }
















































    /**
    * Notify Admins and Drivers of that specific area
    */
    public static function notifyNewBooking($order)
    {  
        $driver_ids = self::getUserRoleIDs('driver');
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');
        
        $adminMessage = $order->customer->full_name. ' just created new booking #'.$order->id;
        $customerMessage = "Booking ID: #".$order->id. ". We will contact you soon.";

        // Send Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'new_booking', $id, $adminMessage,'web');
        }

        // Send Notification to All Drivers of that particular area
        foreach($driver_ids as $id){
            self::notifyApp($order, 'new_booking', $id, $adminMessage,'mobile');
        }
        
        self::notifyApp($order, 'new_booking', $order->customer_id, $customerMessage,'mobile');
        
        return true;
    }

    /**
    * Notify Admins and Customer of that specific order
    */
    public static function notifyAcceptBooking($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.' has been accepted by '. $order->driver->full_name. ' for pickup.';
        $customerMessage = 'Driver: ' . $order->driver->full_name. ' just accepted your booking #' . $order->id. '.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'booking_accepted', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'booking_accepted', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver starts trip for pickup
    */
    public static function notifyStartTripForPickup($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.' trip for pick has been started by Driver: '. $order->driver->full_name. '.';
        $customerMessage = 'Driver: ' . $order->driver->full_name. ' is on the way to pick you for booking #' . $order->id. '.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'trip_to_pick_location', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'trip_to_pick_location', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver reaches pick location
    */
    public static function notifyArrivedAtPickLocation($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.', Driver: '. $order->driver->full_name. ' has reached pick location.';
        $customerMessage = 'Driver: ' . $order->driver->full_name. ' is waiting for you. Booking #' . $order->id. '.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'arrived_at_pick_location', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'arrived_at_pick_location', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver starts trip for destination
    */
    public static function notifyStartTripForDestination($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.', Driver: '. $order->driver->full_name. ' has started trip for destination';
        $customerMessage = 'Booking #' . $order->id. ', Your trip has started. Enjoy your ride.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'start_trip_for_destination', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'start_trip_for_destination', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver starts trip for destination
    */
    public static function notifyArrivedAtDropLocation($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.', Reached Destination';
        $customerMessage = 'Booking #' . $order->id. ', You have reached the desitnation';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'arrived_at_destination', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'arrived_at_destination', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when payment done
    */
    public static function notifyPaymentDone($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #'.$order->id.', Payment Done and Closed';
        $customerMessage = 'Booking #' . $order->id. ', Your payment has been successfully made.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'payment_successfull', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'payment_successfull', $order->customer_id, $customerMessage,'mobile');

        $customer = User::find($order->customer_id);
        
        $customerMailData = [
            'emailType' => 'booking_completed',
            'name'      => $customer->full_name,
            'email'     => $customer->email,
            'subject'   => "BLACK-BLADGE: Booking Completed",
            'message'   => "Thank you ".$customer->full_name." for using Black Badge. Hope you've enjoying the experience. Here are your Trip Details.",
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));
        
        return true;
    }

    /**
    * Notify when driver adds new drop location
    */
    public static function notifyAddDropLocation($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $dropLocationName = $order->dropLocations()->orderBY('created_at','DESC')->first()->drop_location['name'];

        $adminMessage = 'Booking Order #'.$order->id.', Added new drop location '. $dropLocationName;
        $customerMessage = 'Booking #' . $order->id. ', New destination '.$dropLocationName.' added to your ride.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'added_drop_location', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'added_drop_location', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver adds time
    */
    public static function notifyAddTime($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $extendedMinutes = $order->bookingExtendedTime()->orderBy('created_at','DESC')->first()->minutes;
        $adminMessage = 'Booking Order #'.$order->id.', Extended time by ' . $extendedMinutes . 'minutes.';
        $customerMessage = 'Booking #' . $order->id. ', Your trip has been extended by ' . $extendedMinutes . ' minutes.';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'extended_time', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'extended_time', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify when driver cancels order
    */
    public static function notifyDriverCancelBooking($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $cancelDriver = $order->bookingLogs()->where('type','assign_cancel')->orderBy('created_at','DESC')->first()->user->full_name;
        $adminMessage = 'Booking Order #'.$order->id.', cancelled by Driver ' .  $cancelDriver;
        $customerMessage = 'Booking #' . $order->id. ', cancelled by Driver ' .  $cancelDriver;

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'pickup_cancelled', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'pickup_cancelled', $order->customer_id, $customerMessage,'mobile');

        return true;
    }

    /**
    * Notify Admins if customer cancels booking order
    */
    public static function notifyBookingCancelled($order)
    {  
        $superAdmin_ids = self::getUserRoleIDs('superAdmin');

        $adminMessage = 'Booking Order #' . $order->id . ' has been cancelled by '. $order->customer->full_name;
        $customerMessage = 'Your Booking #' . $order->id . ', has been cancelled';

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            self::notifyApp($order, 'order_cancelled', $id, $adminMessage,'web');
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'order_cancelled', $order->customer_id, $customerMessage,'mobile');
        
        return true;
    }
    


















    
    
    /**
    * Notify Admins and Driver of that specific order
    */
    public static function notifyAssignedForDelivery($order_id)
    {  
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $driver_id = $order->drop_driver_id;

        $notifyDriver = [
            'notifyType' => 'assigned_for_delivery',
            'message' => 'Order #'.$order->id.' has been assigned to you for delivery on '.$order->drop_date,
            'model' => 'order',
            'url' => $order->id
        ];

        $notificationAdmin = [
            'notifyType' => 'assigned_for_delivery',
            'message' => 'Order #'.$order->id.' has been assigned to '.$order->dropDriver->fname.' for delivery on '.$order->drop_date,
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notificationAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($driver_id)->AppNotification($notifyDriver);
        return true;
    }


    /**
    * Notify Admins and Customer of that specific order
    */
    public static function notifyReferralBonus($coupon)
    {  
        $notifyCustomer = [
            'notifyType' => 'referral_bonus',
            'message'    => 'Congratulations! One of your referral just completed his new order, You have been rewarded with AED '.$coupon->discount.'. You can claim this reward amount by using our Promo code '.$coupon->code.' before '.\Carbon\Carbon::parse($coupon->valid_to)->format('M-d-Y').' on your next order.',
            'model' => 'coupon',
            'url' => $coupon->id
        ];
        
        // Send Order Accepted Notification to Customer
        User::find($coupon->user_id)->AppNotification($notifyCustomer);

        // Email Notification to Customer
        $customer = User::find($coupon->user_id);
        $customerMailData = [
            'emailType' => 'referral_bonus',
            'name'      => $customer->full_name,
            'email'     => $customer->email,
            'subject'   => "BLACK-BLADGE: Congratulations! You have been awarded with Referral Bonus",
            'message'   => 'Congratulations! One of your referral just completed his new order, You have been rewarded with AED '.$coupon->discount.'. You can claim this reward amount by using our Promo code '.$coupon->code.' before '.\Carbon\Carbon::parse($coupon->valid_to)->format('M-d-Y').' on your next order.'
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));

        return true;
    }
}
