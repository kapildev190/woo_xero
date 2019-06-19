<?php

    define ( 'BASE_PATH', dirname(__FILE__) );
   
    include BASE_PATH . '/private.php';

    // All wharehouse
    $totalInvociceAmt = 0;
    $randNumber = rand(10,1000);
    $poLinks = array(); 
    $invItemListXml = '';

    foreach ($powhline as $whkey => $wh) {
     
        $addCustomerXml =  "<Contacts>
                            <Contact>
                                <Name>".$whkey."</Name>
                                <FirstName>".$whkey."</FirstName>
                            </Contact>
                        </Contacts>";

        $response = $XeroOAuth->request('POST', $XeroOAuth->url('Contacts', 'core'), array(), $addCustomerXml);
        $poContact = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
       
       
        if ($XeroOAuth->response['code'] == 200){
            $contact = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            if (count($contact->Contacts[0])>0)
            {
                $wpcontact['ContactID'] = $ContactID = $contact->Contacts[0]->Contact->ContactID;
                $xeroResponse['status']['contact'] = 'success'; 
                $xeroResponse['response']['contact'] = $poContact;      
                $xeroStatus = "success"; 
            }
        } else {
            $xeroStatus = "fail";
            $xeroResponse['status']['contact'] = 'fail';  
            $xeroResponse['response']['contact'] = $poContact;    
        }
        
        $poItemListXml = '';
       
        foreach ($wh['lines'] as $line) {
            $totalInvociceAmt = $totalInvociceAmt + ($line["qty"]*$line["unitAmount"]);                        
            $poItemListXml    .=  "<LineItem>
                <ItemCode>".$line["Part_Code"]."</ItemCode>
                <Description>".$line["Purchase_Description"]."</Description>
                <Quantity>".$line["qty"]."</Quantity> 
                <AccountCode>300</AccountCode>               
            </LineItem>";

            $invItemListXml    .=  "<LineItem>
                        <ItemCode>".$line["Part_Code"]."</ItemCode>
                        <Description>".$line["Sales_Description"]."</Description>
                        <Quantity>".$line["qty"]."</Quantity>
                        <AccountCode>200</AccountCode>
                    </LineItem>";                                  
        }
      
        $ponumber =  $randNumber."-PO-".$whkey;
        $purchaseOrderXml = "<PurchaseOrder>
                            <PurchaseOrderNumber>".$ponumber."</PurchaseOrderNumber>                             
                            <Reference>".$poReference."</Reference>                             
                            <Contact>
                                <ContactID>".$wpcontact['ContactID']."</ContactID>
                            </Contact>
                            <Date>".date('Y-m-d')."</Date>
                            <LineItems>".$poItemListXml ."</LineItems>
                        </PurchaseOrder>";
    
        $response = $XeroOAuth->request('POST', $XeroOAuth->url('PurchaseOrders', 'core'), array(), $purchaseOrderXml);
        $poRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
        

        if ($XeroOAuth->response['code'] == 200)
        {
            $purchaseOrder = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);            
            $po_id = $purchaseOrder->PurchaseOrders->PurchaseOrder->PurchaseOrderID;
            $po_name = $purchaseOrder->PurchaseOrders->PurchaseOrder->PurchaseOrderNumber;
            $xeroResponse['status']['po'][$whkey] = 'success';
            $xeroResponse['po_id']['po'][$whkey] =  $po_id;
            $xeroResponse['response']['po'][$whkey]  =  $poRes;     
            $xeroStatus = "success";            
           	$poLinks[$whkey]['id'] = $po_id;
           	$poLinks[$whkey]['name'] = $po_name;
        }
        else
        { 
            $xeroResponse['status']['po'][$whkey]      = 'fail';
            $xeroResponse['response']['po'][$whkey]    =  $poRes;
            $xeroStatus = "fail";
        }
        
    }

    // If Instalation Required
    if($isInstallation == 'yes'){

         $addCustomerXml =  "<Contacts>
                            <Contact>
                                <Name>Placeholder Installer</Name>
                                <FirstName>Installation</FirstName>
                            </Contact>
                        </Contacts>";

        $response = $XeroOAuth->request('POST', $XeroOAuth->url('Contacts', 'core'), array(), $addCustomerXml);
        $poInstalContactRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
        if ($XeroOAuth->response['code'] == 200){
            $contact = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            if (count($contact->Contacts[0])>0)
            {
                $installer['ContactID'] = $ContactID = $contact->Contacts[0]->Contact->ContactID;
                $xeroResponse['status']['Installer']['contact'] = 'success'; 
                $xeroResponse['response']['Installer']['contact'] = $poInstalContactRes;      
            }
        } else {
            // outputError($XeroOAuth);
            $_SESSION["yfm_po_contact_create_errro"] = 1;
            $xeroResponse['status']['Installer']['contact'] = 'fail';  
            $xeroResponse['response']['Installer']['contact'] = $poInstalContactRes;
        }

        $instalationCharge = 100;
        $totalInvociceAmt = $totalInvociceAmt + $instalationCharge;
        $ponumber =  $randNumber."-PO-Installation";

        $purchaseOrderXml = "<PurchaseOrder>
                            <PurchaseOrderNumber>".$ponumber."</PurchaseOrderNumber>                             
                             <Reference>".$poReference."</Reference>                                 
                            <Contact>
                                <ContactID>".$installer['ContactID']."</ContactID>
                            </Contact>
                            <Date>".date('Y-m-d')."</Date>
                            <LineItems>
                                <LineItem>
                                    <Description>Installation Charge</Description>
                                    <Quantity>1</Quantity>
                                    <UnitAmount>".$instalationCharge."</UnitAmount>
                                </LineItem>
                            </LineItems>
                        </PurchaseOrder>";

         $invItemListXml    .=  "<LineItem>
                                        <Description>Installation Charge</Description>
                                        <Quantity>1</Quantity>
                                        <UnitAmount>$instalationCharge</UnitAmount>
                                        <AccountCode>200</AccountCode>
                                    </LineItem>";

        $response = $XeroOAuth->request('POST', $XeroOAuth->url('PurchaseOrders', 'core'), array(), $purchaseOrderXml);
        $poInstalRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
        if ($XeroOAuth->response['code'] == 200)
        {
            $purchaseOrder = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            $po_id = $purchaseOrder->PurchaseOrders->PurchaseOrder->PurchaseOrderID;
            $po_name = $purchaseOrder->PurchaseOrders->PurchaseOrder->PurchaseOrderNumber;
            $xeroResponse['status']['po']['PO-Installation'] = 'success';
            $xeroResponse['po_id']['po']['PO-Installation'] =  $po_id;
            $xeroResponse['response']['po']['PO-Installation']    =  $poInstalRes;
            $xeroStatus = "success";
            
            $poLinks['Installation']['id'] = $po_id;
            $poLinks['Installation']['name'] = $po_name;

        }
        else
        {
            $xeroResponse['status']['po']['PO-Installation']     = 'fail';
            $xeroResponse['response']['po']['PO-Installation']    =  $poInstalRes;  
            $xeroStatus = "fail";
        }    
    }
     
    // Invocie create 

    $addCustomerXml =  "<Contacts>
                            <Contact>
                                <Name>".$wlud->data->airtable_ContactName."</Name>
                                <FirstName>".$wlud->data->airtable_ContactName."</FirstName>
                            </Contact>
                        </Contacts>";
                        
        $response = $XeroOAuth->request('POST', $XeroOAuth->url('Contacts', 'core'), array(), $addCustomerXml);
        $invoiceContactRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
        if ($XeroOAuth->response['code'] == 200){
            $contact = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
            if (count($contact->Contacts[0])>0)
            {
                $invoiceContact['ContactID'] = $ContactID = $contact->Contacts[0]->Contact->ContactID;
                $xeroResponse['status']['invoice']['contact'] = 'success'; 
                $xeroResponse['response']['invoice']['contact'] = $invoiceContactRes;  
            }
        } else {
            // outputError($XeroOAuth);
            $_SESSION["yfm_po_contact_create_errro"] = 1;
            $xeroResponse['status']['invoice']['contact'] = 'fail';  
            $xeroResponse['response']['invoice']['contact'] = $invoiceContactRes;    
        }

    $invoiceNumber = $randNumber."-INV";
    $xeroxmldata = "<Invoices>
                        <Invoice>
                            <Type>ACCREC</Type>
                            <Contact>
                                <ContactID>".$invoiceContact['ContactID']."</ContactID>
                            </Contact>
                            <Reference>".$ponum."</Reference>     
                            <Date>" . date('Y-m-d') . "T00:00:00</Date>
                            <DueDate>" . date('Y-m-d') . "T00:00:00</DueDate>
                            <InvoiceNumber>" . $invoiceNumber . "</InvoiceNumber>
                            <LineAmountTypes>Inclusive</LineAmountTypes>
                            <SubTotal>" .  $totalInvociceAmt . "</SubTotal>
                            <TotalTax>0</TotalTax>
                            <Total>" . $totalInvociceAmt . "</Total>
                            <LineItems>
                                ". $invItemListXml."
                            </LineItems>
                        </Invoice>
                    </Invoices>";                    

    $response = $XeroOAuth->request('POST', $XeroOAuth->url('Invoices', 'core'), array(), $xeroxmldata);
    $invoiceRes = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
    if ($XeroOAuth->response['code'] == 200) {
        $xeroResponse['status']['invoice']      = 'success';
        $xeroResponse['response']['invoice']    =  $invoiceRes; 
        $invLinks['id'] = $invoiceRes->Invoices->Invoice->InvoiceID;
        $invLinks['name'] = $invoiceRes->Invoices->Invoice->InvoiceNumber;
    }
    else {
        $xeroResponse['status']['invoice']     = 'fail';
        $xeroResponse['response']['invoice']    =  $invoiceRes;    
    }       
    //echo "<prE>"; print_r( $invoiceRes ); echo "</pre>"; 
    //echo "<prE>"; print_r( $xeroResponse ); echo "</pre>"; 
    //die;

