public function sendSMS(Request $request, $siteId)
{
  $recipients_array = array();
  $message = $request->input('message');
  $recipients = $request->input('recipientsArr');
 
  $SMS = new SMS();
  $SMS->recipients = implode(",", $recipients);
  $SMS->message = $message;
  $SMS->draft_site = $siteId;
  $SMS->save();

  return redirect()->back()->with("success", "SMS sent successfully");
}
