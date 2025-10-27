<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmitted;
use App\Mail\ContactFormConfirmation;
use Illuminate\Validation\ValidationException;

class ContactController2 extends Controller
{
    /**
     * Handle submission of contact form.
     */
    public function submit(Request $request)
    {
        // dd($request->all());


        DB::beginTransaction();
        try {
            Log::info('Contact form data received:', $request->all());

            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'enquiry_type' => 'required|string',
                'enquiry' => 'required|string'
            ]);

            // Create contact record
            $contact = Contact::create($validated);

            Log::info('Contact created', ['contacts' => $contact]);

            // Send email to admin
            Mail::to('osura@eweblook.com')->send(new ContactFormSubmitted($contact));

            // Send confirmation to user
            // Mail::to($contact->email)->send(new ContactFormConfirmation($contact));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been sent successfully.',
                'data' => $contact
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contact submission failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the form: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display all contact messages for admin.
     */
    public function allMessages()
    {
        $contacts = Contact::all();
        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Show a single contact message.
     */
    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update the status of a contact message.
     */
    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $contact->update(['status' => $request->status]);

        return redirect()->route('admin.contacts')->with('success', 'Status updated successfully.');
    }
}