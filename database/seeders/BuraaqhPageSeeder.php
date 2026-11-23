<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BuraaqhPageSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()
            ->whereHas('roles', fn ($query) => $query->where('slug', 'administrator'))
            ->orderBy('id')
            ->value('id');

        $pages = [
            [
                'title' => 'About Buraaqh Properties',
                'slug' => 'about',
                'focus_keyword' => 'real estate consultant in Bareilly',
                'content' => <<<'HTML'
<p>Buraaqh Properties is a Bareilly-focused real estate information and enquiry platform for people exploring residential and commercial property decisions. The website brings together property guidance, locality checklists and a clear way to share requirements. Published property information is a starting point for discussion; buyers and sellers should independently verify ownership, approvals, availability, pricing and transaction terms before making a commitment.</p>
<h2>Company overview</h2>
<p>Property decisions involve more than choosing a building or a plot. The right option depends on location, intended use, budget, documents, construction condition, access, utilities and the responsibilities that come with ownership. Buraaqh Properties is structured around these practical questions so visitors can organise their requirements and identify the information they need to verify.</p>
<p>Our website focuses on the Bareilly property market and the needs of people comparing homes, land and commercial spaces. It provides an enquiry channel and educational resources rather than presenting unverified listings, invented transaction histories or unsupported market forecasts.</p>
<h2>Our mission</h2>
<p>Our mission is to make the early stages of a property decision clearer. We aim to help visitors describe what they need, understand the questions worth asking and approach property information with care. Clear communication and careful verification should come before a viewing, payment or agreement.</p>
<h2>Our vision</h2>
<p>Our vision is a more informed property-search experience in Bareilly, where buyers, sellers and occupiers can compare relevant details, ask better questions and understand when independent legal, financial or technical advice is required.</p>
<h2>Our professional approach</h2>
<ul>
<li><strong>Start with the requirement.</strong> Understand the intended use, preferred property type, broad location, budget range and expected timeline before discussing options.</li>
<li><strong>Keep information specific.</strong> A property should be assessed on its own address, documents, condition, permitted use and current availability. General locality descriptions are not a substitute for verification.</li>
<li><strong>Encourage due diligence.</strong> Ownership records, land use, approvals, taxes, building condition and written transaction terms should be checked with the relevant authorities and qualified professionals.</li>
<li><strong>Communicate clearly.</strong> Questions, next steps and any information still awaiting confirmation should be recorded rather than assumed.</li>
<li><strong>Respect informed decisions.</strong> No website description can guarantee appreciation, rental income, legal title or a successful transaction.</li>
</ul>
<h2>Residential and commercial property guidance</h2>
<p>Residential buyers may need to compare apartments, independent houses, builder floors, villas and plots. Commercial users may be assessing permitted activity, access, visibility, fit-out requirements, operating costs and the terms of occupation. The factors are different, so each enquiry should be considered against its intended purpose rather than a generic list of features.</p>
<p>Our educational resources cover property selection, locality assessment, document checks and the questions to ask before appointing a real estate consultant. Any specific assistance, listing or service should be confirmed directly with Buraaqh Properties before a visitor relies on it.</p>
<h2>Why choose Buraaqh Properties</h2>
<p>Visitors can use one place to share their requirements, explore practical property guidance and prepare for a more focused conversation. We place emphasis on clear information, locally framed questions and responsible verification. We do not publish fictional customer reviews, invented staff biographies, unverified property inventory or guaranteed investment outcomes.</p>
<h2>Begin with a clear enquiry</h2>
<p>If you are considering residential or commercial real estate in Bareilly, share the property type, preferred locality, intended use and the questions you need answered through the contact form. Do not send passwords, bank details or other sensitive financial information. Current availability and the scope of any assistance will be confirmed directly.</p>
HTML,
                'legacy_content' => '<p>Buraaqh Properties is a Bareilly-focused property information and enquiry website. Published listings and service pages are managed through the website CMS. Availability, ownership, approvals and transaction details should be independently verified before a decision is made.</p><h2>Our purpose</h2><p>To make property information easier to review and property enquiries easier to submit.</p><h2>Using this website</h2><p>Review the details shown on each published listing, ask questions through the contact form and confirm all material facts directly before making payments or commitments.</p>',
            ],
            [
                'title' => 'Contact Buraaqh Properties',
                'slug' => 'contact',
                'focus_keyword' => 'contact property dealers in Bareilly',
                'content' => <<<'HTML'
<p>Use this page to send a property enquiry to Buraaqh Properties in Bareilly, Uttar Pradesh. A useful enquiry explains what you are looking for and how the team may contact you. The website does not publish an unverified office address, telephone number or social profile; use the contact details you choose to provide in the form.</p>
<h2>What to include in your enquiry</h2>
<ul>
<li>The type of property you are considering, such as an apartment, independent house, plot or commercial space.</li>
<li>Your preferred locality or a short list of areas you would like to compare.</li>
<li>Your intended use, approximate budget range and expected decision timeline, if you are comfortable sharing them.</li>
<li>The questions you want answered or the information you would like to verify.</li>
</ul>
<h2>Before arranging a visit or making a payment</h2>
<p>Confirm the exact property address, current availability, ownership and authority of the person offering it, applicable approvals, total costs and written terms. Arrange independent legal or technical advice where appropriate. Do not treat a website description or an enquiry response as proof of title, approval or investment performance.</p>
<h2>Send your message</h2>
<p>Complete the enquiry form with a name and at least one reliable contact method. Do not include passwords, bank credentials, identity-document numbers or other sensitive financial information in the message. Any response time, viewing arrangement or service scope should be confirmed directly.</p>
HTML,
                'legacy_content' => '<p>Share your property requirements through the website enquiry form. Please provide accurate contact details so the team can respond to your request.</p>',
            ],
            [
                'title' => 'Buy Property with Buraaqh Properties',
                'slug' => 'buy-with-buraaqh',
                'focus_keyword' => 'properties for sale in Bareilly',
                'content' => <<<'HTML'
<p>A considered property purchase begins with a clear brief. Before comparing properties for sale in Bareilly, decide how the property will be used, which locations are practical for your routine, what total cost is manageable and which documents must be checked. Buraaqh Properties provides this page as a planning resource and enquiry route; the availability and scope of any specific buying assistance must be confirmed directly.</p>
<h2>Define the purchase requirement</h2>
<p>Record the property type, preferred locality, approximate size, household or business needs and expected timeline. Separate essential requirements from preferences. Include recurring costs and likely repairs in the budget rather than comparing options on asking price alone.</p>
<h2>Compare the property and its surroundings</h2>
<p>Visit the exact address. Check the approach, access, utilities, drainage, construction condition, parking arrangements and the practical journey to places you regularly need. Visit at more than one time of day where possible. Conditions can vary within a locality, so do not rely on a neighbourhood name as proof that a particular facility or connection is nearby.</p>
<h2>Verify documents independently</h2>
<p>Ask for the ownership chain, relevant registered documents, land-use information, sanctioned plans where applicable, tax records and details of any loan, charge or dispute. The documents depend on the property type and transaction. Have a qualified property lawyer review the originals and verify relevant records with the appropriate authority.</p>
<h2>Understand the full transaction cost</h2>
<p>Consider stamp duty, registration charges, taxes, brokerage if applicable, legal review, loan costs, society or maintenance charges, utility connections, fit-out and immediate repairs. Request a written breakdown and understand when each amount becomes payable.</p>
<h2>Keep the agreement clear</h2>
<p>Before paying a booking amount or signing, read the written terms, refund conditions, payment schedule, possession date, inclusions and responsibilities of each party. Confirm the seller's authority to transact and retain receipts for every payment. Do not rely on verbal promises to resolve missing documents or unclear conditions.</p>
<h2>Make the decision at your pace</h2>
<p>Compare the evidence, ask follow-up questions and obtain independent advice. No consultant or advertisement can guarantee future appreciation, rental income, loan approval or a risk-free purchase. A careful decision is one where the important facts and unresolved questions are visible before you commit.</p>
HTML,
                'legacy_content' => '<p>Start by recording your preferred location, budget and everyday requirements. Use the contact form to share your enquiry; the team can confirm whether assistance is available. Verify each property and its documents independently before making a decision.</p>',
            ],
            [
                'title' => 'Sell Property with Buraaqh Properties',
                'slug' => 'sell-with-buraaqh',
                'focus_keyword' => 'sell residential and commercial property in Bareilly',
                'content' => <<<'HTML'
<p>Preparing to sell a property is easier when the information is complete and consistent. Owners considering a sale in Bareilly should organise ownership records, property specifications, current photographs and a realistic understanding of the property's condition before discussing marketing or enquiries. Buraaqh Properties provides this page as a preparation guide; confirm directly whether a particular selling service is available and what it includes.</p>
<h2>Prepare the property information</h2>
<p>Write down the exact address, plot or built-up area, property type, age where known, floor details, parking, utilities, occupancy status and any common-area or maintenance obligations. Distinguish measured facts from estimates. If a detail is uncertain, say so rather than presenting it as verified.</p>
<h2>Organise ownership and approval records</h2>
<p>Collect the registered ownership documents, previous deeds where relevant, identity and authority documents, tax receipts, sanctioned plans and approvals applicable to the property. Note any mortgage, charge, tenancy, dispute or restriction that may affect a sale. Ask a qualified professional to review the documents and explain what must be resolved before proceeding.</p>
<h2>Set a supportable asking position</h2>
<p>Compare genuinely similar properties in the immediate area, taking account of location, size, condition, legal status and transaction timing. Asking prices in advertisements are not the same as completed sale values. Avoid relying on unsupported appreciation claims or a single online estimate.</p>
<h2>Present the property accurately</h2>
<p>Use current photographs that represent the actual property. Make access arrangements safely, disclose material defects and clarify which fixtures, fittings or items are included. If work has been completed or approvals obtained, keep supporting records available for review.</p>
<h2>Review offers and written terms</h2>
<p>Confirm the buyer's proposed timeline, payment method, conditions, possession expectations and any financing dependencies. Have the sale agreement reviewed independently. Record every payment and do not hand over possession or original documents outside the agreed legal process.</p>
<h2>Discuss the next step</h2>
<p>If you want to enquire about selling, share the property type, locality and a brief description through the contact form. Do not send original documents or sensitive identity or banking information through a general website message. The business can confirm whether it can assist and what information is needed next.</p>
HTML,
                'legacy_content' => '<p>Prepare accurate property details, current photographs and relevant ownership records before marketing. Use the contact form to share your enquiry and confirm the next steps directly with the business.</p>',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'focus_keyword' => 'Buraaqh Properties privacy policy',
                'content' => <<<'HTML'
<p>This notice explains, in general terms, how the Buraaqh Properties website may handle information submitted through its forms and information required for normal website operation. The site owner should review this notice against the services, hosting providers, analytics tools and retention practices actually used by the live website and obtain appropriate legal advice before relying on it as a final privacy policy.</p>
<h2>Information submitted by visitors</h2>
<p>When you send an enquiry, the website may store the name, email address, telephone number, subject, message and any property or preferred-contact details you choose to provide. Please submit only information relevant to your enquiry. Do not send passwords, bank credentials, payment-card information or unnecessary identity-document details through a general contact form.</p>
<h2>Website operation and technical information</h2>
<p>The website may process session identifiers, security-related information, request metadata and page-view information to maintain the service, protect forms, diagnose technical issues and understand aggregate site usage. The precise data collected depends on the hosting, logging, security and analytics configuration enabled by the site owner.</p>
<h2>How enquiry information may be used</h2>
<p>Information submitted through a form may be used by authorised site administrators to review, respond to and manage the enquiry. It should not be treated as a property reservation, offer, agreement or confirmation of availability. Any further use or disclosure should be limited to a legitimate operational purpose and the site's applicable legal obligations.</p>
<h2>Access, security and retention</h2>
<p>Access to enquiry and account information should be restricted to authorised users. The site owner should maintain suitable access controls, backups and security updates and define a retention period appropriate to the purpose for which records are held. No internet transmission or storage system can be described as completely risk-free.</p>
<h2>Third-party services and external links</h2>
<p>If the live website uses external maps, analytics, social platforms, hosting providers or embedded content, those providers may process information under their own terms and privacy notices. The site owner should identify the services actually enabled and provide links to the relevant notices where required.</p>
<h2>Questions and updates</h2>
<p>The site owner should publish a verified privacy contact channel before launch. This notice may be updated when the website's forms, tools or data practices change. The effective date and any legally required visitor choices should be added after the final review.</p>
HTML,
                'legacy_content' => '<p>When you submit an enquiry, the website stores the name, email address or phone number, subject, message and any property reference or preferred viewing details you provide. Authorized site administrators can review these records to handle the enquiry.</p><p>The website may also store essential session and visit identifiers and page-view records to support site operation and usage reporting. Do not submit sensitive personal or financial information through the enquiry form.</p><p>The site owner should add a verified privacy contact channel and obtain legal review of this notice before launch.</p>',
            ],
            [
                'title' => 'Frequently Asked Questions',
                'slug' => 'frequently-asked-questions',
                'focus_keyword' => 'property buying questions Bareilly',
                'content' => <<<'HTML'
<p>Property decisions often involve several stages: defining the requirement, comparing locations, inspecting the property, reviewing documents and agreeing written terms. The answers on this website are general information to help you prepare questions. They are not legal, tax, lending or engineering advice and do not confirm the status of any specific property.</p>
<h2>How do I enquire about a property?</h2>
<p>Use the contact form and include the property link or reference, if one is shown, along with your question. Ask the business to confirm current availability, the exact address and the source and date of the information.</p>
<h2>What should I verify before paying a booking amount?</h2>
<p>Understand the seller's authority, relevant ownership documents, written booking terms, refund conditions, payment schedule and any approvals applicable to the property. Obtain independent professional advice before transferring money.</p>
<h2>Can the website confirm legal title or approvals?</h2>
<p>No website description should be treated as a legal title opinion or an official approval. Verify original records with the relevant authority and ask a qualified local property lawyer to review the documents.</p>
<h2>Can I request a site visit?</h2>
<p>You can share your preferred property and timing through the enquiry form. A visit should be arranged only after the address, access, availability and the person authorised to show the property have been confirmed.</p>
HTML,
                'legacy_content' => '<h2>How do I ask about a property?</h2><p>Use the contact form and include the published property link or reference. The business can confirm current availability and any next steps.</p><h2>Can I submit a property for consideration?</h2><p>Share the property details through the contact form. The business can confirm whether listing assistance is available.</p>',
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::withTrashed()->where('slug', $data['slug'])->first();
            $legacyContent = $data['legacy_content'];
            unset($data['legacy_content']);

            // A soft-deleted page is an administrator decision. Do not restore
            // it or attempt a duplicate insert against its unique slug.
            if ($page?->trashed()) {
                continue;
            }

            $page ??= new Page(['slug' => $data['slug']]);

            if (! $page->exists) {
                $page->fill($data + [
                    'author_id' => $authorId,
                    'template' => 'default',
                    'status' => 'published',
                    'meta_title' => Str::limit($data['title'].' | Buraaqh Properties', 60, ''),
                    'meta_description' => Str::limit(strip_tags($data['content']), 155),
                ])->save();

                continue;
            }

            // Upgrade only the exact short copy previously generated by this seeder.
            // Any administrator-edited page content and publication state is preserved.
            if ($page->content === $legacyContent) {
                $page->forceFill([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'meta_title' => Str::limit($data['title'].' | Buraaqh Properties', 60, ''),
                    'meta_description' => Str::limit(strip_tags($data['content']), 155),
                    'focus_keyword' => $data['focus_keyword'],
                ])->save();
            }
        }
    }
}
