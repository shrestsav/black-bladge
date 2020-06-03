<?php

namespace App\Traits;

use App\DeviceToken;
use App\Mail\notifyMail;
use App\Order;
use App\User;
use Mail;

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
       
    public static function notifyApp($order, $notifyType, $notifyID, $message)
    {
        $notification = [
            'notifyType' => $notifyType,
            'message'    => $message,
            'model'      => 'order',
            'url'        => $order->id
        ];

        // Send Order Accepted Notification to Customer    
        User::find($notifyID)->AppNotification($notification); 
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
            self::notifyApp($order, 'new_booking', $id, $adminMessage);
        }

        // Send Notification to All Drivers of that particular area
        foreach($driver_ids as $id){
            self::notifyApp($order, 'new_booking', $id, $adminMessage);
        }
        
        self::notifyApp($order, 'new_booking', $order->customer_id, $customerMessage);
        
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
            self::notifyApp($order, 'booking_accepted', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'booking_accepted', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'trip_to_pick_location', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'trip_to_pick_location', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'arrived_at_pick_location', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'arrived_at_pick_location', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'start_trip_for_destination', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'start_trip_for_destination', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'arrived_at_destination', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'arrived_at_destination', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'payment_successfull', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'payment_successfull', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'added_drop_location', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'added_drop_location', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'extended_time', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'extended_time', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'extended_time', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'extended_time', $order->customer_id, $customerMessage);

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
            self::notifyApp($order, 'order_cancelled', $id, $adminMessage);
        }

        // Send Order Accepted Notification to Customer    
        self::notifyApp($order, 'order_cancelled', $order->customer_id, $customerMessage);
        
        return true;
    }

    /**
    * Notify Admins
    */
    public static function notifyCancelForPickup($order_id)
    {  
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $notification = [
            'notifyType' => 'pickup_cancelled',
            'message' => $order->pickDriver->fname. ' has cancelled pickup for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notification);
        }
        
        return true;
    }

    /**
    * Notify Admins and Driver of that specific order
    */
    public static function notifyAssignedForPickup($order_id)
    {  
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $customer_id = $order->customer_id;
        $driver_id = $order->driver_id;

        $notifyCustomer = [
            'notifyType' => 'booking_accepted',
            'message' => 'Your Order #'.$order->id.' has been accepted by '. $order->pickDriver->fname. ' for pickup, please keep your items ready.',
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyDriver = [
            'notifyType' => 'assigned_for_pickup',
            'message' => 'Order #'.$order->id. ' has been assigned to you for pickup',
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyAdmin = [
            'notifyType' => 'assigned_for_pickup',
            'message' => 'Order #'.$order->id. ' has been assigned to '.$order->pickDriver->fname.' for pickup',
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notifyAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($customer_id)->AppNotification($notifyCustomer);

        // Send Order Assigned Notification to Driver
        User::find($driver_id)->AppNotification($notifyDriver); 

        // Email Notification to Customer
        $customer = User::find($order->customer_id);
        $customerMailData = [
            'emailType' => 'booking_accepted',
            'name'      => $customer->full_name,
            'email'     => $customer->email,
            'orderID'   => $order_id,
            'subject'   => "BLACK-BLADGE: Order: #".$order_id. " Accepted",
            'message'   => 'Your Order #'.$order->id.' has been accepted by '. $order->pickDriver->fname. ' for pickup, please keep your items ready.'
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));

        return true;
    }
    // assign order left

    /**
    * Notify Admins and Customer of that specific order
    */
    public static function notifyInvoiceGenerated($order_id)
    {  
        //All Admins and Customer who ordered will get notified
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $customer_id = $order->customer_id;

        $notificationAdmin = [
            'notifyType' => 'invoice_generated',
            'message' => $order->pickDriver->fname. ' has generated an invoice for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyCustomer = [
            'notifyType' => 'invoice_generated',
            'message' => 'An Invoice has been generated for your order, please check and confirm your order',
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notificationAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($customer_id)->AppNotification($notifyCustomer);
        return true;
    }

    /**
    * Notify Admins and Driver of that specific order
    */
    public static function notifyInvoiceConfirmed($order_id)
    {  
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $driver_id = $order->driver_id;

        $notification = [
            'notifyType' => 'invoice_confirmed',
            'message' => $order->customer->fname. ' has confirmed invoice for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Invoice Confirmed Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notification);
        }

        // Send Invoice Confirmed Notification to Pick Driver
        User::find($driver_id)->AppNotification($notification);

        // Email Notification to Customer
        $customer = User::find($order->customer_id);
        $customerMailData = [
            'emailType'     => 'invoice_confirmed',
            'name'          => $customer->full_name,
            'email'         => $customer->email,
            'orderID'       => $order_id,
            'subject'       => "BLACK-BLADGE: Order: #".$order_id. " shipped for laundry",
            'message'       => "Your Order #".$order_id. " has been shipped for laundry. We will contact you soon",
            'orderDetails'  => $order->generateInvoiceForUser()
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));

        return true;
    }

    /**
    * Notify Admins and Customer of that specific order
    */
    public static function notifyDroppedAtOffice($order_id)
    {  
        $order = Order::find($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $customer_id = $order->customer_id;

        $notificationAdmin = [
            'notifyType' => 'dropped_at_office',
            'message' => $order->pickDriver->fname. ' has dropped clothes at office for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyCustomer = [
            'notifyType' => 'dropped_at_office',
            'message' => 'Your clothes for order has been sent for washing',
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notificationAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($customer_id)->AppNotification($notifyCustomer);
        
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
    public static function notifyPickedFromOffice($order_id)
    {  
        //All Admins and Customer who ordered will get notified
        $order = Order::findOrFail($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $customer_id = $order->customer_id;

        $notificationAdmin = [
            'notifyType' => 'picked_from_office',
            'message' => $order->dropDriver->fname. ' has picked clothes from office for delivery for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyCustomer = [
            'notifyType' => 'picked_from_office',
            'message' => 'Your clothes for Order #'.$order->id.' is on process of delivery',
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notificationAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($customer_id)->AppNotification($notifyCustomer);

        return true;
    }

    /**
    * Notify Admins and Customer of that specific order
    */
    public static function notifyDroppedAtCustomer($order_id)
    {  
        //All Admins and Customer who ordered will get notified
        $order = Order::findOrFail($order_id);
        $superAdmin_ids = User::whereHas('roles', function ($query) {
                                    $query->where('name', '=', 'superAdmin');
                                })
                                ->pluck('id')
                                ->toArray();

        $customer_id = $order->customer_id;

        $notificationAdmin = [
            'notifyType' => 'delivered_to_customer',
            'message' => $order->dropDriver->fname.' has delivered clothes to customer '.$order->customer->fname.' for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        $notifyCustomer = [
            'notifyType' => 'delivered_to_customer',
            'message' => 'Clothes delivered for Order #'.$order->id,
            'model' => 'order',
            'url' => $order->id
        ];

        // Send Order Accepted Notification to All Superadmins
        foreach($superAdmin_ids as $id){
            User::find($id)->pushNotification($notificationAdmin);
        }
        // Send Order Accepted Notification to Customer
        User::find($customer_id)->AppNotification($notifyCustomer);

        // Email Notification to Customer
        $customer = User::find($order->customer_id);
        $customerMailData = [
            'emailType' => 'delivered_to_customer',
            'name'      => $customer->full_name,
            'email'     => $customer->email,
            'orderID'   => $order_id,
            'subject'   => "BLACK-BLADGE: Order: #".$order_id. " Delivered",
            'message'   => "Your Order #".$order_id. " has been delivered to you. If you have any queries contact our support team."
        ];
        
        // Notify Customer in email
        Mail::send(new notifyMail($customerMailData));

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
