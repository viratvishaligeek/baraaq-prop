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

            $content = '<p>'.$data['intro'].'</p>'
                .'<h2>Scope to confirm</h2><p>'.$data['scope'].'</p>'
                .'<h2>Potential benefits</h2><p>'.$data['benefits'].'</p>'
                .'<h2>Proposed process</h2><p>'.$data['process'].'</p>';

            if ($faqs !== []) {
                $content .= '<h2>Frequently asked questions</h2>';
                foreach ($faqs as [$question, $answer]) {
                    $content .= '<h3>'.$question.'</h3><p>'.$answer.'</p>';
                }
            }

            $content .= '<h2>Before proceeding</h2><p>Confirm the actual service availability, responsible person, scope, fees and any third-party involvement directly with Buraaqh Properties. Legal, financial, engineering and authority decisions should be handled by the appropriately qualified professional or competent authority.</p>'
                .'<h2>Discuss your requirement</h2><p>Use the contact form to describe your requirement and the questions you would like to discuss. Do not send sensitive financial or identity information through the general enquiry form.</p>';

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

            foreach ($data['features'] as $featureIndex => [$title, $description]) {
                ServiceFeature::query()->firstOrCreate(
                    ['service_id' => $service->id, 'title' => $title],
                    ['description' => $description, 'sort_order' => $featureIndex + 1]
                );
            }
        }
    }
}
