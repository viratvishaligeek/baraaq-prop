<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BuraaqhServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Property Search and Shortlisting',
                'slug' => 'property-search-shortlisting',
                'excerpt' => 'A structured way to describe a property requirement and compare options against practical criteria.',
                'keyword' => 'property search Bareilly',
                'intro' => 'A property search is more effective when the requirement is clear. This draft service description outlines a possible process for recording preferences, comparing property information and identifying questions that need verification.',
                'scope' => 'A search brief may cover intended use, property type, preferred locality, broad budget, size, access, essential features and expected timeline. Any property option should be considered only after its current availability and source information are confirmed.',
                'benefits' => 'A written brief can reduce irrelevant options, make comparisons more consistent and help visitors identify missing information before arranging a site visit.',
                'process' => 'The proposed process is to record the requirement, agree the comparison criteria, review information supplied for candidate properties, note unanswered questions and confirm whether a site visit can be arranged. Each step and the actual service scope must be agreed with the business before publication.',
                'preparation' => 'Before a discussion, note the areas you can realistically consider, whether the requirement is for living or business use, the preferred property type, a broad budget ceiling, must-have features and the date by which you need to move. Keep optional preferences separate from non-negotiable requirements. Do not share identity documents or sensitive financial information through a general enquiry form.',
                'steps' => [
                    ['Clarify the brief', 'Confirm intended use, decision-makers, budget range, property type, preferred areas and timing.'],
                    ['Agree comparison criteria', 'Choose the factors that matter and record which information must be verified for every option.'],
                    ['Review candidate information', 'Compare only options with a traceable source and note availability, price and document details that still need confirmation.'],
                    ['Prepare next actions', 'Create a shortlist of questions and arrange a viewing only after the property and access details are reconfirmed.'],
                ],
                'features' => [
                    ['Requirement brief', 'Record intended use, property type, preferred area, budget range and essential needs.'],
                    ['Comparable shortlist', 'Compare available information using consistent criteria rather than headline descriptions alone.'],
                    ['Open-question log', 'Keep a written list of details that still need confirmation from the owner or relevant authority.'],
                    ['Visit preparation', 'Prepare questions about access, condition, utilities, measurements and documents before a site visit.'],
                ],
            ],
            [
                'title' => 'Property Sale Preparation',
                'slug' => 'property-sale-preparation',
                'excerpt' => 'A checklist-led approach to organising property details and documents before a sale discussion.',
                'keyword' => 'sell property Bareilly',
                'intro' => 'Preparing a property for sale begins with accurate information. This draft service description sets out a possible preparation framework for owners considering a sale; the business must confirm whether it offers this assistance.',
                'scope' => 'Preparation may include collecting property specifications, current photographs, ownership and approval records, known maintenance information, access arrangements and a written list of questions for professional review.',
                'benefits' => 'Consistent information can make enquiries easier to handle, reduce avoidable misunderstandings and help prospective buyers identify the records they need to review independently.',
                'process' => 'The proposed steps are to document the property, identify missing records, agree how viewings and enquiries will be handled, and ensure that material details are represented accurately. Asking-price advice or legal certification is not included unless separately confirmed and appropriately qualified.',
                'preparation' => 'Owners should gather the current property description, available title and approval records, the area measurement basis, a list of fixtures included in the sale, known repairs and any occupancy or tenancy details. Identify information that is uncertain rather than estimating or presenting it as verified. Use recent photographs of the actual property.',
                'steps' => [
                    ['Record the property accurately', 'Prepare a factual description of the address, layout, condition, area basis and included fixtures.'],
                    ['Inventory available records', 'List the documents supplied, their dates and the person or authority from whom they came.'],
                    ['Identify open issues', 'Record missing approvals, unclear measurements, known defects or other questions for professional review.'],
                    ['Agree enquiry handling', 'Confirm who will respond to enquiries, arrange viewings and keep prospective buyers updated.'],
                ],
                'features' => [
                    ['Property information sheet', 'Organise address, area basis, layout, condition and included fixtures.'],
                    ['Document checklist', 'List available title, approval, tax and maintenance records for independent review.'],
                    ['Photography readiness', 'Use current photographs that accurately represent the property being offered.'],
                    ['Enquiry preparation', 'Prepare consistent answers and a record of details that remain unverified.'],
                ],
            ],
            [
                'title' => 'Property Documentation Coordination',
                'slug' => 'property-documentation-coordination',
                'excerpt' => 'Help organising the questions and records that buyers and sellers may need to review with qualified professionals.',
                'keyword' => 'property documentation Bareilly',
                'intro' => 'Property documentation can involve several records and several parties. This draft service description is limited to organising information and coordinating questions; it does not represent legal advice or a title verification service.',
                'scope' => 'A coordination checklist may identify the documents relevant to the property type, note missing items, record questions for a lawyer and track the status of requested information.',
                'benefits' => 'An organised file can help the parties and their independent advisers work from consistent information and make gaps visible before a binding commitment.',
                'process' => 'The proposed process is to identify the transaction type, prepare a document request list, record the source and date of supplied records, and refer legal questions to an independent qualified professional. The business must confirm the actual scope before this page is published.',
                'preparation' => 'The relevant records depend on the property and transaction. A starting file may include prior conveyance documents, current revenue or municipal records where applicable, sanctioned plans and completion or occupancy records where applicable, tax and utility receipts, society or association statements, and the draft transaction terms. A qualified local lawyer should decide which records are legally relevant.',
                'steps' => [
                    ['Identify the transaction and property type', 'Record whether the matter concerns a plot, independent building, apartment, leasehold interest or commercial premises.'],
                    ['Create a document register', 'List each record, its date, source, format and whether an original or certified copy needs to be inspected.'],
                    ['Route legal questions', 'Send title, transfer, land-use and approval questions to the appointed qualified lawyer or competent authority.'],
                    ['Track outstanding actions', 'Record who is responsible for each missing document or clarification and the agreed follow-up date.'],
                ],
                'features' => [
                    ['Document inventory', 'Create a list of records supplied and records still requested.'],
                    ['Source tracking', 'Record who supplied each item and when it was received.'],
                    ['Question register', 'Prepare clear questions for the buyer’s or seller’s independent legal adviser.'],
                    ['Milestone coordination', 'Track agreed document-related actions without certifying legal sufficiency.'],
                ],
            ],
            [
                'title' => 'Residential Property Consultation',
                'slug' => 'residential-property-consultation',
                'excerpt' => 'A discussion framework for comparing residential requirements, property condition and ownership considerations.',
                'keyword' => 'residential property consultant Bareilly',
                'intro' => 'A residential property consultation should begin with the household’s needs rather than a generic property list. This draft page describes a discussion framework for administrator review, not a claim that a particular consultant or appointment is currently available.',
                'scope' => 'Topics may include household size, usable space, accessibility, location preferences, construction condition, utilities, maintenance, total cost and the documents that should be independently reviewed.',
                'benefits' => 'A structured discussion can help households distinguish essential needs from preferences and prepare a consistent comparison of candidate homes.',
                'process' => 'The proposed process is to record the requirement, identify suitable comparison criteria, list property-specific questions and agree the next step. Site visits, availability and any professional referrals must be confirmed separately.',
                'preparation' => 'Households can prepare by listing who will use the home, daily travel patterns, accessibility needs, room and storage requirements, preferred building type, maintenance tolerance and a realistic total-cost range. Include one or two examples of what would make a property unsuitable so that the discussion can focus on practical trade-offs.',
                'steps' => [
                    ['Understand household priorities', 'Separate essential needs from preferences and identify constraints such as access, stairs or move-in timing.'],
                    ['Set comparison questions', 'Prepare a consistent checklist for layout, natural light, ventilation, visible condition, utilities and shared facilities.'],
                    ['Review candidate information', 'Compare the available facts and flag details that cannot be established from a listing or conversation.'],
                    ['Plan independent checks', 'Identify the inspections, document reviews and cost estimates needed before a decision.'],
                ],
                'features' => [
                    ['Household requirement review', 'Record space, layout, access and everyday-use priorities.'],
                    ['Location checklist', 'Compare exact streets and routes against the household’s routine.'],
                    ['Condition questions', 'Prepare an inspection checklist for visible defects and maintenance.'],
                    ['Ownership review prompts', 'Identify records for independent legal review before a commitment.'],
                ],
            ],
            [
                'title' => 'Commercial Property Consultation',
                'slug' => 'commercial-property-consultation',
                'excerpt' => 'A planning framework for assessing intended commercial use, access, operating needs and transaction terms.',
                'keyword' => 'commercial property consultant Bareilly',
                'intro' => 'Commercial property suitability depends on the activity that will take place at the premises. This draft service description outlines questions an occupier or buyer may need to consider; permitted use and the availability of assistance must be independently confirmed.',
                'scope' => 'A commercial brief may cover intended activity, customer and staff access, visibility, loading, utilities, fit-out, operating hours, lease or purchase terms and relevant permissions.',
                'benefits' => 'A use-led brief can expose practical constraints early and help compare premises on the requirements that matter to the business.',
                'process' => 'The proposed process is to record the intended activity, identify property requirements, list permissions and technical questions for qualified advisers, and compare confirmed information. No licence, approval or permitted-use status is guaranteed by this page.',
                'preparation' => 'Describe the exact business activity, customer and staff profile, expected operating hours, equipment, power and water needs, loading or storage requirements, signage needs and fit-out plans. The same premises can be suitable for one activity and unsuitable for another, so do not rely on a broad “commercial” label.',
                'steps' => [
                    ['Define the intended activity', 'Record the business use, operating pattern, staffing, customer access and equipment requirements.'],
                    ['Assess the premises against operations', 'Compare access, visibility, layout, utilities, loading, parking and recurring charges at the exact address.'],
                    ['List approval questions', 'Identify land-use, building, fire, safety, trade and other permission questions for the competent authorities and advisers.'],
                    ['Review transaction obligations', 'Record lease or purchase terms, deposits, fit-out rights, repairs, permitted use and exit conditions for legal review.'],
                ],
                'features' => [
                    ['Business-use brief', 'Describe the activity, customer profile, equipment and operating needs.'],
                    ['Premises comparison', 'Compare access, layout, utilities, visibility and recurring charges.'],
                    ['Permission checklist', 'Identify land-use, building, safety and licensing questions for the relevant authority.'],
                    ['Lease or purchase review prompts', 'Record questions about term, deposit, escalation, fit-out and exit obligations.'],
                ],
            ],
            [
                'title' => 'Property Site-Visit Planning',
                'slug' => 'property-site-visit-planning',
                'excerpt' => 'Prepare a focused site visit with questions about access, condition, measurements, utilities and supporting records.',
                'keyword' => 'property site visit Bareilly',
                'intro' => 'A site visit is most useful when visitors know what they need to observe and what must be confirmed separately. This draft service description outlines a visit-planning checklist and must be approved by the business before publication.',
                'scope' => 'Planning may include confirming the address and access, preparing a list of visible-condition checks, recording measurement questions and identifying documents to request after the visit.',
                'benefits' => 'A consistent checklist helps visitors compare properties and reduces the chance that important practical questions are forgotten during a short viewing.',
                'process' => 'The proposed process is to confirm the visit details, prepare the checklist, record observations and open questions, and arrange any specialist inspection separately. A visual visit is not a structural survey or legal verification.',
                'preparation' => 'Before travelling, reconfirm the exact address, current availability, access arrangements and the identity of the person authorised to show the property. Prepare questions about the areas you need to measure, the condition of visible components, utilities, common areas, parking and documents to request after the visit.',
                'steps' => [
                    ['Confirm the appointment', 'Recheck the property address, contact person, access instructions and whether the property is still available.'],
                    ['Use a property-specific checklist', 'Record observations about layout, light, ventilation, fittings, visible dampness, cracks and maintenance.'],
                    ['Separate observations from conclusions', 'Note what you saw and what needs a qualified structural, electrical, plumbing or other inspection.'],
                    ['Record follow-up requirements', 'List measurements, records, approvals and answers that must be obtained before making an offer.'],
                ],
                'features' => [
                    ['Visit checklist', 'Prepare questions about layout, light, ventilation, fittings and maintenance.'],
                    ['Access review', 'Observe the exact approach, parking and shared areas.'],
                    ['Utility questions', 'Confirm water, electricity, drainage and service arrangements.'],
                    ['Follow-up record', 'List documents and technical checks still required after the visit.'],
                ],
            ],
        ];

        foreach ($services as $index => $data) {
            $faqs = [
                'property-search-shortlisting' => [
                    ['Can a shortlist guarantee that a property is still available?', 'No. Availability can change. Confirm the exact property, current terms and the person authorised to show or offer it before travelling or making a payment.'],
                    ['Does a comparison replace document checks?', 'No. A shortlist helps organise options; ownership, approvals, land use and transaction documents require independent verification.'],
                ],
                'property-sale-preparation' => [
                    ['Does preparation include a certified valuation?', 'No. A formal valuation should be obtained from a suitably qualified valuer where required. An information sheet is not a valuation certificate.'],
                    ['Should photographs show the actual property?', 'Yes. Use current photographs that accurately represent the property and disclose material condition issues rather than relying on edited or unrelated images.'],
                ],
                'property-documentation-coordination' => [
                    ['Does this service replace a property lawyer?', 'No. Title opinions, legal drafting and legal advice must come from a qualified lawyer appointed for the transaction.'],
                    ['Does a document checklist prove ownership?', 'No. It records which documents have been supplied; the originals and relevant authority records must still be independently reviewed.'],
                ],
                'residential-property-consultation' => [
                    ['Can a consultation confirm that a home is suitable?', 'A discussion can organise requirements and questions, but the buyer must inspect the exact property and verify its condition, documents and costs.'],
                    ['Can future appreciation be predicted with certainty?', 'No. Future prices and rental outcomes are uncertain and should not be guaranteed.'],
                ],
                'commercial-property-consultation' => [
                    ['Does a commercial listing confirm that my business use is permitted?', 'No. Verify the permitted use, licences, building approvals and any restrictions with the competent authority and qualified professionals.'],
                    ['Should I review a lease before paying a deposit?', 'Yes. Understand the term, deposit, escalation, permitted use, fit-out, repair obligations and exit clauses in writing before paying.'],
                ],
                'property-site-visit-planning' => [
                    ['Is a site visit the same as a technical inspection?', 'No. A viewing can help identify visible questions, but structural, electrical and other technical conclusions require an independent qualified inspector.'],
                    ['What should I confirm before travelling?', 'Confirm current availability, the exact address, access arrangements and the identity of the person authorised to show the property.'],
                ],
            ][$data['slug']] ?? [];

            $additionalFaqs = [
                'property-search-shortlisting' => [
                    ['Should I pay a token amount to reserve a shortlisted property?', 'Do not pay based only on a shortlist or an informal message. First confirm the seller or authorised representative, inspect the relevant original records with your lawyer, understand the written terms and use a traceable payment method only after the required checks.'],
                    ['Can the service compare properties in every Bareilly locality?', 'The brief can record preferred localities, but available options and service coverage must be confirmed directly. A locality name alone is not evidence of a particular road condition, facility or property attribute.'],
                ],
                'property-sale-preparation' => [
                    ['Should I hide a defect until a buyer asks?', 'No. Material known condition issues should be described accurately. A preparation checklist is intended to organise information, not conceal defects or create misleading advertising.'],
                    ['Can this service decide the asking price?', 'No asking price or valuation is established by this checklist. If a formal valuation is needed, consult a suitably qualified valuer and compare relevant evidence.'],
                ],
                'property-documentation-coordination' => [
                    ['Which documents are required for every property transaction?', 'There is no single checklist that fits every property. The records depend on ownership history, land category, property type, approvals and transaction structure. Ask the appointed local property lawyer to identify the applicable records.'],
                    ['Can a scan or photocopy replace inspection of originals?', 'Not automatically. The relevant originals or certified records should be examined as advised by the lawyer and verified with the issuing authority where appropriate.'],
                ],
                'residential-property-consultation' => [
                    ['Should I compare only the quoted purchase price?', 'No. Also consider applicable taxes and charges, repairs, utility costs, maintenance contributions, commute needs and the cost of any work required before occupation. Verify amounts from current documents.'],
                    ['Can the consultation certify building quality?', 'No. Visible observations and discussion prompts are not a structural or technical certificate. Arrange an independent inspection by an appropriately qualified professional.'],
                ],
                'commercial-property-consultation' => [
                    ['Can I assume a property advertised as commercial permits my specific activity?', 'No. Confirm the sanctioned use and all applicable permissions for the exact premises and proposed activity with the competent authority before signing or fitting out.'],
                    ['What should a commercial occupier check in the written terms?', 'Review permitted use, lease term, deposit, escalation, maintenance, taxes, utilities, fit-out consent, repair duties, signage, renewal and exit provisions with a qualified adviser.'],
                ],
                'property-site-visit-planning' => [
                    ['Should I rely on a viewing to assess structural safety?', 'No. A viewing can reveal visible questions but cannot certify structural safety. Engage an independent qualified professional for a technical inspection where needed.'],
                    ['What should I record during a viewing?', 'Record the date and time, who provided access, rooms and areas inspected, visible issues, measurements still needed, utility questions, documents promised and the person responsible for each follow-up.'],
                ],
            ][$data['slug']] ?? [];

            $legacyContent = '<p>'.$data['intro'].'</p>'
                .'<h2>Scope to confirm</h2><p>'.$data['scope'].'</p>'
                .'<h2>Potential benefits</h2><p>'.$data['benefits'].'</p>'
                .'<h2>Proposed process</h2><p>'.$data['process'].'</p>';

            if ($faqs !== []) {
                $legacyContent .= '<h2>Frequently asked questions</h2>';
                foreach ($faqs as [$question, $answer]) {
                    $legacyContent .= '<h3>'.$question.'</h3><p>'.$answer.'</p>';
                }
            }

            $legacyContent .= '<h2>Before proceeding</h2><p>Confirm the actual service availability, responsible person, scope, fees and any third-party involvement directly with Buraaqh Properties. Legal, financial, engineering and authority decisions should be handled by the appropriately qualified professional or competent authority.</p>'
                .'<h2>Discuss your requirement</h2><p>Use the contact form to describe your requirement and the questions you would like to discuss. Do not send sensitive financial or identity information through the general enquiry form.</p>';

            $content = '<p>'.$data['intro'].'</p>'
                .'<h2>Service scope</h2><p>'.$data['scope'].'</p>'
                .'<h2>Potential benefits</h2><p>'.$data['benefits'].'</p>'
                .'<h2>What to prepare</h2><p>'.$data['preparation'].'</p>'
                .'<h2>Proposed process</h2><ol>';

            foreach ($data['steps'] as [$stepTitle, $stepDescription]) {
                $content .= '<li><strong>'.$stepTitle.'</strong> — '.$stepDescription.'</li>';
            }

            $content .= '</ol><h2>Frequently asked questions</h2>';

            foreach (array_merge($faqs, $additionalFaqs) as [$question, $answer]) {
                $content .= '<h3>'.$question.'</h3><p>'.$answer.'</p>';
            }

            $content .= '<h2>Service features</h2><p>The service highlights below describe the proposed work areas. The final deliverables, responsible person, fees and timing must be agreed in writing before the service is offered.</p>'
                .'<h2>Important limits</h2><p>This service description is not legal advice, a title certificate, a formal valuation, a structural survey or an approval from a government authority. Those matters must be handled by the relevant qualified professional or competent authority.</p>'
                .'<h2>Discuss your requirement</h2><p>Contact Buraaqh Properties to confirm whether this service is currently available, who will handle it, what is included, the applicable fees and the expected next step. Do not send identity documents or sensitive financial information through the general enquiry form.</p>';

            $existing = Service::withTrashed()->where('slug', $data['slug'])->first();
            if ($existing?->trashed()) {
                continue;
            }

            $service = $existing ?? Service::query()->firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $content,
                    'featured_image' => null,
                    'status' => 'draft',
                    'published_at' => null,
                    'meta_title' => Str::limit($data['title'].' | Buraaqh Properties Bareilly', 60, ''),
                    'meta_description' => Str::limit($data['excerpt'], 155, ''),
                    'focus_keyword' => $data['keyword'],
                ]
            );

            if ($service->wasRecentlyCreated) {
                foreach ($data['features'] as $featureIndex => [$title, $description]) {
                    ServiceFeature::query()->firstOrCreate(
                        ['service_id' => $service->id, 'title' => $title],
                        ['description' => $description, 'sort_order' => $featureIndex + 1]
                    );
                }
            } elseif ($service->status === 'draft' && $service->content === $legacyContent) {
                // Upgrade only the exact previous generated draft. Administrator edits remain untouched.
                $service->update([
                    'content' => $content,
                    'meta_title' => Str::limit($data['title'].' | Buraaqh Properties Bareilly', 60, ''),
                    'meta_description' => Str::limit($data['excerpt'], 155, ''),
                    'focus_keyword' => $data['keyword'],
                ]);
            }
        }
    }
}
