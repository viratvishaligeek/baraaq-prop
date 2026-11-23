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
                'title' => 'Property Valuation Discussion',
                'slug' => 'property-valuation',
                'excerpt' => 'Prepare a property profile and a comparable-evidence checklist before discussing an asking price or purchase offer.',
                'legacy_body' => 'Receive a considered valuation based on location, comparable sales, property condition and current buyer demand.',
                'intro' => 'A useful price discussion begins with accurate property information and evidence from genuinely comparable transactions. This outline describes a possible information-gathering process; it is not a certified valuation, survey or promise of a sale price.',
                'scope' => ['Record the exact location, property type, plot or built-up area and known age of construction.', 'Separate verified measurements and documents from estimates or information supplied verbally.', 'Identify comparable properties by location, size, condition, legal status and transaction timing.', 'List visible repairs, access constraints, occupancy and other factors that may affect a buyer’s assessment.'],
                'benefits' => 'A structured property profile helps owners and buyers discuss the same facts. It can reveal missing information, highlight differences between asking prices and completed transactions and identify questions that need professional review.',
                'process' => ['Share the property type, locality and basic specifications.', 'Provide only records that are appropriate to share through an agreed secure channel.', 'Confirm which details can be independently checked and which remain unverified.', 'Use the resulting checklist as one input to a price discussion; obtain a qualified valuation where required.'],
                'faqs' => [['Is this a certified valuation?', 'No. A certified valuation must be obtained from a suitably qualified valuer where one is required.'], ['Can an asking price be guaranteed?', 'No. A market discussion cannot guarantee buyer interest, a final sale price or a transaction date.']],
                'features' => [
                    ['Property profile', 'Organise location, area, property type, condition and occupancy details in one reviewable brief.'],
                    ['Comparable evidence', 'Compare like-for-like information and record the source and date of each reference.'],
                    ['Open questions', 'Identify missing measurements, approvals, repairs or documents before relying on a price estimate.'],
                ],
            ],
            [
                'title' => 'Property Documentation Assistance',
                'slug' => 'legal-assistance',
                'excerpt' => 'Organise property records and questions for review by an independent lawyer or the relevant registration professional.',
                'legacy_body' => 'We help you organise the questions and documents to discuss with your appointed legal and registration professionals.',
                'intro' => 'Property paperwork can involve registered deeds, prior ownership records, land-use information, building permissions, tax records and lender documents. This page describes a document-coordination outline, not a legal practice or a substitute for advice from a qualified property lawyer.',
                'scope' => ['Prepare a property-specific checklist of documents requested by your appointed lawyer.', 'Organise available copies by document type, date and issuing authority.', 'Record differences in names, area, boundaries or property descriptions that need explanation.', 'Track questions about title, encumbrances, land use, approvals and dues for professional review.'],
                'benefits' => 'A complete, well-organised file makes it easier for the appointed professional to identify gaps and explain next steps. It also reduces reliance on verbal assurances when a document or authority record is still outstanding.',
                'process' => ['Identify the property and the type of transaction.', 'Ask the lawyer which original records and searches are required.', 'Organise the requested information and note anything unavailable.', 'Wait for the qualified professional’s written advice before signing or paying.'],
                'faqs' => [['Does this replace a lawyer?', 'No. Legal opinions, title verification and drafting advice must come from a qualified lawyer.'], ['Does a checklist prove ownership?', 'No. A checklist only organises records; the underlying documents and official records require independent verification.']],
                'features' => [
                    ['Document inventory', 'Create a list of deeds, approvals, tax records and supporting papers relevant to the transaction.'],
                    ['Consistency review', 'Flag differences in names, measurements, dates and property descriptions for professional review.'],
                    ['Question tracking', 'Keep a written list of unresolved title, land-use and approval questions.'],
                ],
            ],
            [
                'title' => 'Home Finance Readiness Information',
                'slug' => 'mortgage-services',
                'excerpt' => 'Prepare a property and affordability brief and understand the information a regulated lender may request.',
                'legacy_body' => 'Understand the information lenders typically require and compare suitable home-finance options with our referral network.',
                'intro' => 'A home-finance decision belongs between the applicant and a regulated lender. This draft page focuses on preparing information and questions before a lender discussion. It does not offer a loan, promise approval or imply a lending partnership.',
                'scope' => ['List the expected purchase cost and the funds available for the transaction.', 'Prepare a basic income, existing-obligation and employment-information checklist for discussion with the lender.', 'Record the property documents and approvals the lender may request.', 'Compare the total repayment, fees, rate type, tenure and prepayment conditions in written lender offers.'],
                'benefits' => 'Preparation helps applicants ask clearer questions and compare the full cost of finance rather than focusing on a headline interest rate. It also makes property-specific lender requirements easier to identify early.',
                'process' => ['Define the approximate property budget and purchase timeline.', 'Ask the chosen lender for its current eligibility and document requirements.', 'Compare written offers directly with the lender and understand every charge.', 'Proceed only after the lender confirms its decision and the property review is complete.'],
                'faqs' => [['Can anyone guarantee loan approval?', 'No. Only the lender can assess an application and issue an approval subject to its conditions.'], ['Does this website provide loans?', 'No loan or lending service is represented as available by this draft content.']],
                'features' => [
                    ['Affordability brief', 'Organise purchase costs, available funds and existing financial commitments for a lender conversation.'],
                    ['Document checklist', 'Record the identity, income and property records the chosen lender requests.'],
                    ['Offer comparison', 'Compare written lender terms, fees, rate changes and repayment conditions.'],
                ],
            ],
            [
                'title' => 'Property Insurance Information',
                'slug' => 'insurance-solutions',
                'excerpt' => 'Prepare questions about property risks, policy coverage, exclusions and the responsibilities of an insurer.',
                'legacy_body' => 'Explore practical home and property protection options with guidance on the cover that may suit your needs.',
                'intro' => 'Insurance needs depend on the property, its use, construction, location, ownership and the policy wording. This information outline is not insurance advice, a policy quotation or a representation that Buraaqh Properties is an insurer or licensed intermediary.',
                'scope' => ['Describe the property type, occupancy and intended use accurately.', 'List the building, contents and liability risks you want to discuss with an insurer.', 'Ask how exclusions, deductibles, underinsurance and claim documentation work.', 'Compare policy wording, limits, exclusions, renewal conditions and claim procedures.'],
                'benefits' => 'Reading the policy rather than relying on a headline premium helps owners understand what is and is not covered. A written comparison can make exclusions and responsibilities easier to see.',
                'process' => ['Prepare a factual property description and any required supporting records.', 'Contact an authorised insurer or intermediary directly.', 'Ask for the policy wording and written answers to coverage questions.', 'Keep the issued policy, premium receipt and claim contact details securely.'],
                'faqs' => [['Does this page recommend a particular policy?', 'No. Coverage should be selected directly with an authorised provider after reviewing the policy wording.'], ['Is every type of damage covered?', 'No. Coverage depends on the policy terms, limits, exclusions and facts of a claim.']],
                'features' => [
                    ['Property risk brief', 'Record the use, construction and features that an insurer may need to assess.'],
                    ['Coverage questions', 'Prepare questions about limits, deductibles, exclusions and claim evidence.'],
                    ['Policy comparison', 'Review the wording and total cost of options supplied by authorised providers.'],
                ],
            ],
            [
                'title' => 'Property Inspection Coordination',
                'slug' => 'home-inspection',
                'excerpt' => 'Prepare a practical inspection checklist and arrange qualified technical review where the property requires it.',
                'legacy_body' => 'Arrange a structured inspection to review visible condition, fittings and maintenance considerations before completion.',
                'intro' => 'A short viewing can reveal obvious concerns, but it cannot establish the structural or technical condition of a building. This outline explains how a buyer can prepare for an inspection and when to appoint an independent engineer, surveyor or other qualified specialist.',
                'scope' => ['Record visible condition of walls, ceilings, floors, doors, windows and external surfaces.', 'Ask about plumbing, electrical systems, water ingress, ventilation and maintenance history.', 'Check access, parking, common areas, fixtures and any work promised by the seller.', 'Arrange specialist inspection for structural, electrical, plumbing or other technical questions.'],
                'benefits' => 'A written inspection record makes it easier to compare properties, request clarification and agree which repairs or further checks are needed before completion.',
                'process' => ['Confirm permission and access for the inspection.', 'Use a consistent checklist and photograph items where permission is given.', 'Refer technical concerns to an appropriately qualified professional.', 'Record agreed repairs and handover conditions in the written transaction documents.'],
                'faqs' => [['Can a visual checklist certify a building as safe?', 'No. Safety and structural conclusions require appropriate qualified inspection.'], ['Should defects be recorded in writing?', 'Yes. Record observations and agree how any repair or further inspection will be handled.']],
                'features' => [
                    ['Visible condition checklist', 'Record the condition of finishes, fittings, access and common areas.'],
                    ['Specialist referrals', 'Identify issues that need an independent engineer or technical professional.'],
                    ['Handover record', 'Track agreed repairs, fixtures, keys and meter readings before possession.'],
                ],
            ],
            [
                'title' => 'Transaction and Handover Coordination',
                'slug' => 'escrow-transaction-support',
                'excerpt' => 'Keep transaction milestones, document exchanges and handover responsibilities clearly recorded.',
                'legacy_body' => 'We coordinate key milestones and communication so buyers and sellers know what is needed at each stage.',
                'intro' => 'A property transaction involves several people, documents, payments and deadlines. This outline focuses on organising communication and handover steps. It does not provide escrow, custody of client funds, banking services or legal representation.',
                'scope' => ['Prepare a timeline of agreed milestones, responsible parties and target dates.', 'Record which documents each party must provide and when they are expected.', 'Keep a written log of questions, agreed changes and outstanding conditions.', 'Prepare a handover checklist covering possession, keys, fixtures, meters and receipts.'],
                'benefits' => 'A shared written timeline can reduce missed steps and confusion about who is responsible for a document or action. It also helps the parties identify unresolved conditions before a deadline arrives.',
                'process' => ['Confirm the transaction parties and the professionals appointed by them.', 'Record the written agreement and milestones supplied by the parties.', 'Track progress without handling or holding transaction funds.', 'Confirm completion and handover only against the parties’ written records.'],
                'faqs' => [['Does this service hold deposits or escrow funds?', 'No. Payments must be made through the parties’ agreed and legally appropriate channels.'], ['Can it replace legal transaction advice?', 'No. The parties should appoint qualified legal and financial professionals for their respective roles.']],
                'features' => [
                    ['Milestone tracker', 'Record agreed dates, responsibilities and outstanding conditions.'],
                    ['Document exchange log', 'Keep a list of records requested, received and awaiting review.'],
                    ['Handover checklist', 'Record possession items, keys, meters, fixtures and receipts.'],
                ],
            ],
            [
                'title' => 'Property Management Enquiry',
                'slug' => 'property-management',
                'excerpt' => 'Define the responsibilities, reporting and maintenance expectations that an owner may want to discuss with a property manager.',
                'legacy_body' => 'From tenant coordination to routine property checks, our management support helps protect your time and asset.',
                'intro' => 'Property management can involve tenant communication, maintenance, inspections, rent administration and records. The responsibilities must be agreed clearly with the owner and any appointed manager. This draft outline does not confirm that a management service, tenant placement or rent collection facility is currently available.',
                'scope' => ['Describe the property, occupancy status and owner’s expectations.', 'List routine maintenance, inspection and emergency-response responsibilities.', 'Clarify who may approve repairs and the spending limit for urgent work.', 'Agree how tenant communication, records, payments and reporting will be handled.'],
                'benefits' => 'A written management brief helps owners understand the work involved, the limits of authority and how issues will be reported. It can also make costs and response expectations easier to compare.',
                'process' => ['Prepare a property and responsibility brief.', 'Confirm the manager’s authority, fees, service area and insurance where relevant.', 'Agree a written management contract and reporting schedule.', 'Keep records of instructions, invoices, inspections and tenant communications.'],
                'faqs' => [['Can a manager guarantee rent or occupancy?', 'No. Tenant demand, payment and vacancy are uncertain and must not be guaranteed.'], ['Who approves major repairs?', 'The written management agreement should identify the owner’s approval rights and any emergency procedure.']],
                'features' => [
                    ['Responsibility schedule', 'Define inspections, routine tasks, reporting and owner approvals.'],
                    ['Maintenance records', 'Track requests, quotations, invoices and completed work.'],
                    ['Communication process', 'Agree how tenant and owner updates are recorded and escalated.'],
                ],
            ],
            [
                'title' => 'Real Estate Consultation',
                'slug' => 'real-estate-consulting',
                'excerpt' => 'Structure a property brief and compare residential or commercial options using clear, verifiable criteria.',
                'legacy_body' => 'Discuss your goals with a specialist who understands local demand, neighbourhoods and the practical details of a move.',
                'intro' => 'A useful property conversation starts with the decision you need to make. Buyers, sellers and commercial occupiers may have different priorities, and a general market statement cannot replace property-specific evidence. This draft page outlines a consultation framework for administrator review.',
                'scope' => ['Clarify the intended use, preferred property type, location and timeline.', 'Separate essential requirements from flexible preferences.', 'Identify the information needed to compare shortlisted options fairly.', 'List legal, financial and technical questions that should be referred to qualified professionals.'],
                'benefits' => 'A written brief makes it easier to focus a search, compare alternatives and identify missing evidence. It can also help prevent decisions based only on an attractive photograph or an unsupported forecast.',
                'process' => ['Share the decision you are considering and the broad requirements.', 'Agree the scope, fees, information sources and limits of any consultation in writing.', 'Review available information and record unresolved questions.', 'Use independent professional advice for legal, tax, lending and technical matters.'],
                'faqs' => [['Does a consultation guarantee a suitable property?', 'No. Availability, suitability and transaction outcomes depend on the facts and the parties involved.'], ['Can market appreciation be guaranteed?', 'No. Future values and rental income are uncertain and should not be promised.']],
                'features' => [
                    ['Requirement discovery', 'Create a brief covering use, location, budget, size and timeline.'],
                    ['Option comparison', 'Compare available information against the same practical criteria.'],
                    ['Decision checklist', 'Record due-diligence questions and the independent advice still required.'],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::withTrashed()->where('slug', $serviceData['slug'])->first();
            $isNew = ! $service;

            $content = $this->renderService($serviceData);

            if ($isNew) {
                $service = Service::query()->create([
                    'title' => $serviceData['title'],
                    'slug' => $serviceData['slug'],
                    'excerpt' => $serviceData['excerpt'],
                    'content' => $content,
                    'featured_image' => null,
                    'status' => 'draft',
                    'published_at' => null,
                    'meta_title' => Str::limit($serviceData['title'].' | Buraaqh Properties Bareilly', 255, ''),
                    'meta_description' => Str::limit($serviceData['excerpt'], 155, ''),
                ]);
            } elseif ($service->trashed()) {
                continue;
            } else {
                $legacyContent = '<p>'.$serviceData['legacy_body'].'</p><p>Contact Buraaqh Properties to discuss your requirements and the most suitable next step.</p>';

                if ($service->content === $legacyContent) {
                    $service->forceFill([
                        'title' => $serviceData['title'],
                        'excerpt' => $serviceData['excerpt'],
                        'content' => $content,
                        'meta_title' => Str::limit($serviceData['title'].' | Buraaqh Properties Bareilly', 255, ''),
                        'meta_description' => Str::limit($serviceData['excerpt'], 155, ''),
                    ])->save();
                }
            }

            foreach ($serviceData['features'] as $index => [$title, $description]) {
                $feature = ServiceFeature::query()->firstOrCreate(
                    ['service_id' => $service->id, 'title' => $title],
                    ['description' => $description, 'sort_order' => $index + 1]
                );

                $legacyDescription = 'A clearly scoped draft feature for the '.$service->title.' offering.';
                if ($feature->description === $legacyDescription) {
                    $feature->forceFill(['description' => $description])->save();
                }
            }
        }
    }

    private function renderService(array $service): string
    {
        $html = '<p>'.$service['intro'].'</p>';
        $html .= '<p><strong>Publication note:</strong> This service page remains unpublished until the business confirms that the offering, responsible personnel, service area, fees and terms are accurate.</p>';
        $html .= '<h2>What the service may cover</h2><ul>';

        foreach ($service['scope'] as $item) {
            $html .= '<li>'.$item.'</li>';
        }

        $html .= '</ul><h2>Why a clear process matters</h2><p>'.$service['benefits'].'</p>';
        $html .= '<h2>How an enquiry could proceed</h2><ol>';

        foreach ($service['process'] as $step) {
            $html .= '<li>'.$step.'</li>';
        }

        $html .= '</ol><h2>Questions to resolve</h2>';

        foreach ($service['faqs'] as [$question, $answer]) {
            $html .= '<h3>'.$question.'</h3><p>'.$answer.'</p>';
        }

        $html .= '<h2>Next step</h2><p>Use the contact form to describe your requirement. The business must confirm whether this service is currently available, who is responsible for it and what written terms apply before any appointment or payment.</p>';

        return $html;
    }
}
