<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use App\Models\NeighborhoodGuide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BuraaqhLocalContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdditionalFaqs();
        $this->upgradeUneditedLegacyFaqs();
        $this->upgradeUneditedLocalityGuides();
    }

    private function seedAdditionalFaqs(): void
    {
        $faqs = [
            ["How should I decide between a ready-to-move home and an under-construction property?", "Compare your required move-in date, the evidence available about approvals and construction, the payment schedule, the agreement terms, the remaining work and the risks of delay. For either option, have the relevant records reviewed independently and do not rely only on a promised completion date. Ask for dated construction updates and payment milestones, and confirm what the written agreement says if work or approvals are delayed."],
            ["What is the difference between carpet area and built-up area?", "Carpet area generally describes the usable floor area within the internal walls, while built-up area may include wall thickness and other covered portions. The precise definitions used in a transaction should be checked in the applicable documents and rules. Ask the seller to state the area basis clearly and compare like with like. Request the area basis in the agreement and compare only properties measured on the same basis. If a brochure uses more than one area figure, ask the seller to explain each one in writing."],
            ["What should I verify before paying a property token amount?", "Confirm the seller or recipient is authorised, identify the exact property, understand what the payment reserves, review refund and forfeiture terms, and obtain a written receipt and agreement. Have the important documents reviewed before making a payment. Do not rely on a verbal promise about a refund or future approval. Use a traceable payment method and retain the signed receipt and agreement. If a material document or approval is still missing, ask your lawyer whether the payment should be deferred or made conditional."],
            ["Can a mutation entry alone prove ownership?", "A mutation or revenue entry is not, by itself, a complete substitute for reviewing the title documents and the property's legal history. Its significance depends on the record and transaction. Ask an independent property lawyer to examine the relevant original documents and current records together. Ask your lawyer whether a mutation or record update is required after registration and which authority maintains it. Keep the registered deed and the relevant official records together rather than relying on one entry."],
            ["What should I check before buying a residential plot?", "Verify the title chain, exact boundaries, recorded land details, permitted land use, layout or subdivision status, legal access, encumbrances and the approvals required for the intended construction. Have the site and records compared by qualified professionals before you commit. Check whether the intended use and any future construction require additional permission. A layout brochure or nearby construction is not proof that the same use is allowed for your parcel."],
            ["What should I check before leasing a commercial property?", "Confirm that the intended business activity is permitted, review the owner's authority, lease term, rent and escalation, deposit, lock-in, renewal, exit and repair clauses, utilities, fit-out permissions, signage rules and any building or safety requirements that apply to the activity. Also confirm who pays utilities and maintenance, whether subletting or alterations are allowed, how repairs are approved and how disputes or early termination are handled."],
            ["How can I prepare for a property site visit?", "Take a written list of requirements and questions, note the exact address, inspect the approach and common areas, check visible condition and utilities, ask for measurements and supporting records, take notes and record unanswered questions. Arrange a specialist inspection if technical issues need assessment. With permission, take dated photographs of relevant areas and make a written note of defects or unanswered questions. Ask who will provide follow-up documents and by when."],
            ["Should I use the seller’s lawyer for my document review?", "A buyer should consider obtaining independent legal advice from a professional who represents the buyer’s interests. The seller’s adviser may explain the seller’s position, but that does not replace an independent review of title, approvals, restrictions and the agreement for the buyer. Ask your own lawyer to explain any qualification, missing record or risk in writing before you agree. Independent advice is particularly important where the transaction involves multiple owners or unusual authority documents."],
            ["How do I compare property prices in the same locality?", "Compare properties with similar use, exact micro-location, area basis, age, condition, floor, access, approvals and included facilities. Ask for reliable evidence of comparable transactions and account for repairs and recurring charges. An advertised asking price is not proof of a completed market transaction. Adjust the comparison for registration costs, repairs, parking, maintenance, floor, access and the area basis. Keep asking prices separate from evidence of completed transactions."],
            ["Does Buraaqh Properties guarantee property appreciation or rental income?", "No website article or general consultation should be treated as a guarantee of appreciation, rental income, resale value or investment performance. Outcomes depend on the property, market conditions, costs, legal position and many other factors. Obtain independent financial advice and assess conservative scenarios. Treat any projection as an assumption and test whether the decision remains affordable under a less favourable scenario. Do not borrow or commit on the basis of a guaranteed return."],
            ["Can I rely on photographs instead of visiting a property?", "Photographs can help with an initial review but cannot establish the property's current condition, exact dimensions, surroundings, access, utilities or legal status. Visit the specific property and obtain the relevant documents and independent checks before making a decision. Images may be old, edited or taken from a different angle; confirm that they represent the exact unit being offered. A visit also lets you assess the approach, surroundings and access that photographs cannot show."],
            ["What information should I include in a property enquiry?", "Share the property type, intended use, preferred areas, broad budget, required size or features, expected timeline and the questions you need answered. Do not include bank credentials, identity document numbers or other sensitive financial information in a general website enquiry. If you are asking about a published listing, include its reference and the date you last checked it. Share only the information needed to respond and keep sensitive identity or financial details out of a general enquiry."],
            ["Does a property consultation include legal advice?", "A general property consultation should not be treated as legal advice or a title certificate. Ask what the service actually includes and have ownership, land use, approvals and agreement terms reviewed by an independent qualified property lawyer. Ask for the consultant’s role and limitations in writing and keep the legal review independent. A discussion can help organise questions, but it cannot certify title, approvals or the enforceability of an agreement."],
            ["Can a property search guarantee that a suitable listing is available?", "No. Property availability changes and depends on information supplied by owners or authorised representatives. Ask for the date and source of each property detail and confirm current availability before arranging a visit or making a payment. Confirm availability again shortly before arranging a visit and do not assume that an online page is updated in real time. Ask who supplied the information and when it was last checked."],
            ["Is a property site visit the same as a technical inspection?", "No. A viewing helps you observe the property and prepare questions, but it is not a structural, electrical, plumbing or legal inspection. Arrange an appropriately qualified specialist when technical or legal verification is required. A qualified inspector can explain the scope of the inspection and provide a written report. Choose the appropriate specialist for the concern, such as structural, electrical or plumbing condition."],
            ["Can a consultant certify the market value of my property?", "A discussion of asking prices or comparable information is not automatically a formal valuation. If you need a valuation for lending, taxation, accounting or a legal purpose, confirm the required qualification and appoint an appropriately authorised professional. Confirm that the valuer is qualified for the intended purpose and that the report meets any lender, authority or accounting requirements. A general market discussion is not a substitute for a formal valuation."],
            ["How are real estate consultation fees confirmed?", "Ask for the scope, fee amount, applicable taxes, payment timing, refund terms and any referral or commission arrangement in writing before engaging a service. Do not assume that a general enquiry or initial conversation has a particular fee structure. Clarify when the fee becomes due and whether travel, documentation, referral or third-party costs may arise. Keep the agreed fee and payment trigger in writing before work begins."],
            ["Does commercial property guidance confirm that my business can operate at a location?", "No. Permitted use, licences, safety requirements, building approvals and other conditions depend on the exact premises and activity. Confirm the requirements with the relevant authority and qualified professionals before signing a lease or purchase agreement. Check the exact permitted activity, building occupancy status, fire and safety conditions and any local restrictions before signing. Obtain written confirmation from the competent authority where the use requires permission."],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            $existing = FaqItem::withTrashed()->where('question', $question)->first();

            // Do not resurrect a question that an administrator deliberately deleted.
            if ($existing?->trashed() || $existing) {
                continue;
            }

            FaqItem::query()->create([
                'question' => $question,
                'answer' => $answer,
                'category' => 'Property guidance',
                'sort_order' => 20 + $index,
                'is_active' => true,
            ]);
        }
    }

    private function upgradeUneditedLegacyFaqs(): void
    {
        $faqs = [
            [
                'question' => "What should I check before buying a property in Bareilly?",
                'old' => "Review the ownership chain, approved plans where applicable, land-use status, encumbrances, taxes and the terms of the sale agreement. Have the documents independently checked by a qualified local professional.",
                'new' => "Start with the exact property address and the seller’s authority to transfer it. Review the registered ownership documents and relevant prior deeds, land-use information, sanctioned plans where applicable, tax and maintenance dues, and any mortgage, charge or dispute. Inspect the property and its access, utilities and visible condition. Have a qualified local property lawyer review original records and verify relevant details with the competent authorities. The documents required differ for a plot, apartment, independent house and commercial premises, so use a property-specific checklist rather than relying on a generic list.",
            ],
            [
                'question' => "How can I compare two residential properties?",
                'old' => "Compare the exact location, usable area, construction condition, access, utilities, title documents, total transaction cost and resale considerations. Visit each property and verify all seller-provided information.",
                'new' => "Use the same checklist for both properties. Compare the exact street and approach, usable layout, measured area, construction condition, daylight and ventilation, parking, water and electricity arrangements, drainage, maintenance obligations and total acquisition cost. Review the ownership and approval documents for each property independently. Visit both at different times where possible and record unanswered questions. Do not compare only the asking price or a locality name; a lower price may reflect a difference in condition, access, legal status or included facilities.",
            ],
            [
                'question' => "What documents are commonly reviewed during a property purchase?",
                'old' => "Depending on the property and transaction, buyers commonly review title documents, prior sale deeds, sanctioned plans where applicable, tax receipts, identity documents and applicable project or authority approvals. Requirements vary; obtain professional advice.",
                'new' => "The checklist depends on the property and its history. It may include the current registered deed, relevant prior ownership documents, seller identity and authority, revenue or municipal records, land-use information, sanctioned plans, completion or occupancy records where applicable, tax receipts, lender release documents and project disclosures. For a project covered by RERA, check the relevant official registration and disclosures. Ask a qualified property lawyer to identify the records required for the exact transaction and verify originals with the appropriate authorities.",
            ],
            [
                'question' => "Should I pay a booking amount before document verification?",
                'old' => "Do not rely on verbal assurances. Understand the written booking terms, refund conditions, seller authority and relevant documents before making a payment. Seek independent legal advice when needed.",
                'new' => "Do not transfer money simply because a property is described as urgent or in demand. First confirm the identity and authority of the recipient, the exact property, the written booking terms, refund conditions, payment schedule and any conditions that must be met. Ask which documents have been checked and which remain outstanding. Have the terms reviewed independently where appropriate, pay only through a traceable agreed method and retain a receipt. A verbal promise does not replace a written agreement or title review.",
            ],
            [
                'question' => "What affects residential property value in Bareilly?",
                'old' => "Value can depend on micro-location, road access, plot or built-up area, construction quality, legal status, available infrastructure and comparable transactions. Verify current market evidence rather than relying on general estimates.",
                'new' => "A property’s price may be influenced by its exact street, access, plot or usable area, layout, construction condition, permitted use, ownership records, utility arrangements, maintenance costs and the terms of comparable transactions. Conditions can vary within one locality. Compare properties with similar size, age, condition and legal status, and distinguish asking prices from completed transaction evidence. No general locality description can establish a current market value or guarantee future appreciation; obtain a qualified valuation where a formal opinion is needed.",
            ],
            [
                'question' => "Can Buraaqh Properties confirm legal title or government approvals?",
                'old' => "A property decision should be supported by independent document verification and relevant authority records. Do not treat marketing information as a legal opinion or an official approval.",
                'new' => "Website content and general property discussions are not a legal title opinion or an official approval. Verify the registered ownership documents, land-use status, building permissions and other applicable records with the relevant authority, and ask a qualified property lawyer to review the originals. The required checks depend on the exact property and transaction. Do not make a payment or sign an agreement on the assumption that a listing description certifies title or compliance.",
            ],
        ];

        foreach ($faqs as $faqData) {
            $faq = FaqItem::withTrashed()->where('question', $faqData['question'])->first();

            if ($faq && ! $faq->trashed() && $faq->answer === $faqData['old']) {
                $faq->update([
                    'answer' => $faqData['new'],
                    'category' => 'Property guidance',
                ]);
            }
        }
    }

    private function upgradeUneditedLocalityGuides(): void
    {
        $guides = [
            'Civil Lines' => [
                'title' => 'Civil Lines, Bareilly: A Property Evaluation Guide',
                'excerpt' => 'A street-level checklist for comparing access, building use, parking, maintenance and documents when considering property in Civil Lines.',
                'keyword' => 'Civil Lines Bareilly property guide',
                'content' => <<<'HTML'
<p>Use this guide to evaluate a specific property in Civil Lines, Bareilly. It is not a ranking of the locality and does not claim that a particular school, hospital, road project, public service or transport connection is available near every address. Conditions should be checked at the exact street and property.</p>
<h2>Understand the exact address</h2>
<p>Record the full address, lane, approach and the property's position relative to the roads you expect to use. Visit the location on more than one day and at different times. Observe traffic, parking, deliveries, pedestrian movement and noise. A property on a main road can have different access and operating conditions from one inside a quieter lane.</p>
<h2>Check the building's permitted use</h2>
<p>Where residential and commercial activity may be close to one another, verify the permitted use of the exact premises and any restrictions that apply to the building. If you intend to operate a business, ask the competent authority and a qualified professional what permissions are required. Do not infer permitted use from neighbouring buildings.</p>
<h2>Review access and ongoing responsibilities</h2>
<p>Check parking allocation, approach width, emergency access, water, electricity, drainage, waste collection and street lighting. For an apartment or shared building, ask who manages common areas, what maintenance charges apply and whether repairs or disputes are outstanding. For an independent property, understand boundary, access and utility responsibilities.</p>
<h2>Complete property-specific due diligence</h2>
<p>Review the ownership chain, seller authority, applicable plans and approvals, tax records, encumbrances and written transaction terms. Ask a qualified local lawyer to review original records and verify details with the appropriate authority. Compare the property with genuinely similar options using measured area, condition, access and legal status.</p>
<h2>Questions to take on a visit</h2>
<ul><li>What is the exact boundary and measured area?</li><li>Which parking or common areas are included in the transaction?</li><li>What are the current utility and maintenance arrangements?</li><li>Are there any building-use restrictions, dues, disputes or pending repairs?</li><li>Which documents remain to be independently verified?</li></ul>
HTML,
            ],
            'Rajendra Nagar' => [
                'title' => 'Rajendra Nagar, Bareilly: A Residential Property Checklist',
                'excerpt' => 'Questions to ask about everyday convenience, street conditions, building maintenance and ownership records when comparing homes in Rajendra Nagar.',
                'keyword' => 'Rajendra Nagar Bareilly property',
                'content' => <<<'HTML'
<p>This guide helps buyers assess an individual home in Rajendra Nagar, Bareilly. It does not make a claim about current prices, infrastructure, transport links or the availability of any particular facility. A locality name cannot establish the condition or suitability of a specific street, building or plot.</p>
<h2>Match the location to your routine</h2>
<p>Write down the places your household visits regularly and test the actual journeys from the property entrance. Check the route at the times you expect to travel. Consider walking conditions, crossings, lighting, parking and whether the route is practical for children, older family members or anyone with mobility needs.</p>
<h2>Inspect the home and shared spaces</h2>
<p>Look at the roof, ceilings, walls, floors, windows, ventilation, plumbing, electrical fittings and visible signs of dampness. Ask about repairs and maintenance history. If you are considering an apartment or floor, inspect stairways, lifts where relevant, parking, water storage, drainage and the upkeep of common areas.</p>
<h2>Verify utility arrangements</h2>
<p>Ask how water, electricity, sewage, waste collection and internet services are provided to the exact property. Confirm whether any connection is shared and how charges are divided. Request current bills or service information where appropriate. Do not assume that arrangements at one building are the same for another property nearby.</p>
<h2>Review documents and responsibilities</h2>
<p>Check the seller's authority, registered ownership records, property description, boundaries, applicable building permissions, taxes, dues and any mortgage or dispute. Ask about society or maintenance rules, parking rights and any outstanding repair obligations. A qualified local lawyer should review original documents and confirm which authority records apply.</p>
<h2>Compare options consistently</h2>
<p>Use the same checklist for each property and record the visit date, exact address, area, condition, recurring costs and unresolved questions. Compare evidence rather than relying on a general impression or an advertised price. If a future facility or development is mentioned, verify it through an official source before treating it as part of the decision.</p>
HTML,
            ],
            'Izatnagar' => [
                'title' => 'Izatnagar, Bareilly: Questions to Ask Before Buying',
                'excerpt' => 'A practical framework for checking exact-site access, noise, utilities, land status and building documents when assessing property in Izatnagar.',
                'keyword' => 'Izatnagar Bareilly property guide',
                'content' => <<<'HTML'
<p>When considering property in Izatnagar, Bareilly, assess the exact address rather than relying on a broad locality description. This guide does not state that any road, railway connection, facility or project is close to a particular property. It is a checklist of matters to verify during your own visits and document review.</p>
<h2>Check the approach and surroundings</h2>
<p>Visit the property using the route you expect to take regularly. Observe traffic, road surface, pedestrian access, parking and the condition of the immediate lane. Check the location at different times to understand noise, deliveries, activity and access. If road, rail, industrial or other activity could affect the property, assess the actual impact at the building rather than making assumptions from a map.</p>
<h2>Confirm the property type and permitted use</h2>
<p>Identify whether the option is an apartment, independent house, plot, agricultural parcel or commercial premises. Ask what use is legally permitted and whether any conversion, layout approval or other permission is required. For land, verify dimensions, boundaries, access rights and classification with the relevant records and authorities.</p>
<h2>Inspect construction and utilities</h2>
<p>Check visible construction condition, ventilation, water supply, drainage, electricity, internet availability and maintenance arrangements. Ask about recurring issues such as water ingress, access restrictions, waste collection or shared-service charges. For technical concerns, appoint an independent engineer or specialist; a short visit cannot certify the building.</p>
<h2>Review records independently</h2>
<p>Ask for the registered ownership documents, prior deeds relevant to the title chain, land-use information, sanctioned plans where applicable, tax records and details of any loan, charge or dispute. Have a qualified property lawyer review original documents and verify information with the appropriate authority. Do not rely only on a mutation entry, brochure or seller statement.</p>
<h2>Keep a comparison record</h2>
<p>Record the exact address, date of visit, access observations, utility arrangements, property condition, asking terms and outstanding documents. Compare the same factors across each shortlisted property. Treat any statement about future development or appreciation as unverified until supported by a current official source and independent analysis.</p>
HTML,
            ],
            'Pilibhit Bypass' => [
                'title' => 'Pilibhit Bypass, Bareilly: A Property Access and Due-Diligence Guide',
                'excerpt' => 'How to assess approach roads, entry points, daily travel, services and property records for a specific address near Pilibhit Bypass.',
                'keyword' => 'Pilibhit Bypass Bareilly property',
                'content' => <<<'HTML'
<p>A property described as being near Pilibhit Bypass still needs an exact-site assessment. Distance from a main road does not automatically mean convenient access, safe entry or suitability for a household or business. Use this guide to check the specific address and verify all claims independently.</p>
<h2>Test the actual route</h2>
<p>Travel from the property entrance to the roads and destinations you expect to use. Check turning points, service-road arrangements where present, crossing options, pedestrian access, traffic at different times and the condition of the final approach lane. Consider how visitors, deliveries and emergency vehicles would reach the property.</p>
<h2>Assess noise, access and daily comfort</h2>
<p>Visit during different periods and observe traffic noise, dust, lighting, parking and the movement of commercial or residential activity. Check the orientation of the home, window placement and whether the layout provides the privacy and ventilation you need. Do not assume that two properties at similar map distances will have the same conditions.</p>
<h2>Confirm services and utilities</h2>
<p>Ask how water, electricity, drainage, sewage, waste collection and internet are supplied to the exact property. Verify who maintains the approach road and any shared services. Confirm the location and current operating status of the schools, healthcare providers, markets or other facilities that matter to your household.</p>
<h2>Check land and building status</h2>
<p>For plots and developments, verify the land classification, layout approval, boundaries, access rights and permitted use. For constructed properties, compare the actual building with applicable sanctioned plans and completion or occupancy records. Review the ownership chain, seller authority, taxes, encumbrances and any restrictions with a qualified professional.</p>
<h2>Compare total cost, not only the advertised price</h2>
<p>Include registration and applicable government charges, legal review, repairs, utility connections, maintenance and travel costs in your comparison. Ask for written terms and confirm what is included. If a future road, service lane or development project is mentioned, verify its official status before relying on it.</p>
HTML,
            ],
            'DD Puram' => [
                'title' => 'DD Puram, Bareilly: A Checklist for Comparing Homes',
                'excerpt' => 'A practical checklist for comparing the exact block, building condition, parking, utilities, maintenance and documents in DD Puram.',
                'keyword' => 'DD Puram Bareilly property guide',
                'content' => <<<'HTML'
<p>Use this guide when comparing a home in DD Puram, Bareilly. It does not assign a rating to the locality or make a factual claim about current amenities, prices or infrastructure. A sound decision depends on the exact block, street, building and documents of the property you are considering.</p>
<h2>Compare the exact block and street</h2>
<p>Record the full address and approach. Visit at the times you expect to travel and observe traffic, parking, noise, pedestrian access, lighting and the condition of the street. Test the route to the places that matter to your household. Ask about any recurring access or maintenance issue and seek more than one source before relying on informal information.</p>
<h2>Review the building and common areas</h2>
<p>Inspect walls, ceilings, floors, roof, windows, plumbing, electrical fittings and ventilation. Ask about water supply, drainage, repairs and the age of major systems. For a shared building, understand parking allocation, lift arrangements where applicable, common-area maintenance, reserve funds and rules that affect residents.</p>
<h2>Understand costs and ownership responsibilities</h2>
<p>Ask for current property-tax and maintenance information and clarify any outstanding dues. Check which fixtures, parking spaces or storage areas are included in the transaction. Understand the responsibility for repairs, common services and utility connections. For a lease, review escalation, deposit, lock-in, permitted use and exit terms.</p>
<h2>Verify documents and approvals</h2>
<p>Review registered ownership records, seller authority, prior deeds, property description, applicable building plans, completion records, taxes and encumbrances. Have original documents reviewed by a qualified local property lawyer. If a floor, extension or change of use has been made, ask which permissions apply and verify them with the relevant authority.</p>
<h2>Make a consistent comparison</h2>
<p>Use a written checklist for each shortlisted home. Record measured area, visit date, condition, access, utilities, total costs and open document questions. A careful comparison is more useful than a broad claim that one locality or block is always better than another.</p>
HTML,
            ],
            'Bareilly Cantt' => [
                'title' => 'Bareilly Cantt: Property Permission and Document Checks',
                'excerpt' => 'Additional questions to raise about jurisdiction, land status, access and permissions when assessing a property in or around Bareilly Cantt.',
                'keyword' => 'Bareilly Cantt property due diligence',
                'content' => <<<'HTML'
<p>When assessing a property described as being in or around Bareilly Cantt, establish the exact jurisdiction and land status before making assumptions about ownership or permitted use. This guide does not claim that a particular property is restricted, transferable or subject to a specific permission. Those facts must be confirmed for the exact parcel with the competent authority and a qualified local lawyer.</p>
<h2>Identify the exact jurisdiction</h2>
<p>Obtain the full address, plot or property identifier, boundaries and relevant map. Ask which local body or authority controls the land and which office maintains the records. Do not rely on a neighbourhood name, postal address or a seller's verbal description to establish jurisdiction.</p>
<h2>Verify land status and transfer conditions</h2>
<p>Ask whether the property is freehold, leasehold, government-allotted, privately owned or subject to another tenure. If any defence, cantonment, government or other special jurisdiction may be relevant, obtain written clarification from the competent authority about transfer, construction, use and any required consent or no-objection certificate. Do not assume that a document from a different property applies to this parcel.</p>
<h2>Check access, boundaries and use</h2>
<p>Confirm the measured area, physical boundaries, approach rights and permitted use. Inspect the property and compare it with the maps and records. Ask about easements, shared access, restrictions, utilities and any pending dues. For a constructed property, check the approvals and plans relevant to the building and any later additions.</p>
<h2>Obtain independent document review</h2>
<p>Have a qualified local property lawyer examine the registered deeds, title chain, authority documents, land classification, permissions and encumbrance information. Verify original records with the appropriate office. If the seller cannot provide a required document or the authority is unclear, pause the transaction until the issue is resolved in writing.</p>
<h2>Do not rely on assumptions</h2>
<p>Do not make a payment based on an assurance that a transfer or construction permission will be granted later. Confirm current requirements directly with the competent authority and include any necessary conditions in the written agreement. This locality guide is a prompt for due diligence, not a legal opinion about any specific property.</p>
HTML,
            ],
            'Mahanagar' => [
                'title' => 'Mahanagar, Bareilly: A Guide to Property Selection',
                'excerpt' => 'Questions about street conditions, permitted use, utilities, maintenance and ownership records to ask when comparing property in Mahanagar.',
                'keyword' => 'Mahanagar Bareilly property guide',
                'content' => <<<'HTML'
<p>This guide provides a practical framework for evaluating a property in Mahanagar, Bareilly. It is not a locality rating and does not claim that any particular amenity, road, school, hospital or development project is available near a given address. Verify conditions at the exact property before relying on them.</p>
<h2>Understand the immediate surroundings</h2>
<p>Visit the street and the property entrance at different times. Observe traffic, parking, noise, pedestrian access, lighting and the condition of the approach. Check how the location fits your household's routine and test actual journeys to work, education, healthcare and other destinations that matter to you.</p>
<h2>Inspect the home and its services</h2>
<p>Review the layout, measured area, ventilation, natural light, water supply, electricity, drainage, internet and waste arrangements. Inspect visible building condition and ask about repairs or recurring problems. If you are buying in a shared building, understand common-area maintenance, parking, rules and outstanding charges.</p>
<h2>Check land use and approvals</h2>
<p>Confirm the legal use of the property and whether the building matches the applicable sanctioned plan. For plots, verify boundaries, access, land classification and any layout or conversion permissions. For apartments and floors, ask which completion or occupancy records apply. Have original documents reviewed independently.</p>
<h2>Compare ownership costs and written terms</h2>
<p>Prepare a full cost estimate that includes applicable registration charges, legal review, repairs, maintenance, taxes and utility costs. Review the sale or lease terms, payment schedule, possession, inclusions and responsibilities. Ask for outstanding dues and disputes to be disclosed and addressed in writing.</p>
<h2>Keep a record of what remains unverified</h2>
<p>For each option, write down the documents received, authority checks completed, technical questions referred to a specialist and information still awaiting confirmation. Compare alternatives on the same basis. Do not treat a general neighbourhood description or an advertised price as proof of the property's condition or value.</p>
HTML,
            ],
        ];

        $legacyContent = static fn (string $locality): string => '<p>Use this guide as a starting point when assessing a property in '.$locality.'. It is not a rating of the locality or a statement that any particular facility, road, school or project is nearby.</p><h2>What to verify on a visit</h2><ul><li>Check the exact street, approach road and access at different times of day.</li><li>Confirm water, electricity, drainage and other utility arrangements for the specific property.</li><li>Verify land use, title chain, sanctioned plans and applicable approvals independently.</li><li>Compare recent transactions for genuinely comparable properties in the immediate area.</li><li>Check the written terms, total charges and any society or maintenance obligations.</li></ul><p>Local conditions can vary street by street. Confirm current details with relevant authorities and qualified local professionals before making a decision.</p>';

        foreach ($guides as $locality => $data) {
            $slug = Str::slug($locality.' Bareilly property guide');
            $guide = NeighborhoodGuide::withTrashed()->where('slug', $slug)->first();

            if ($guide?->trashed()) {
                continue;
            }

            $attributes = [
                'title' => $data['title'],
                'locality' => $locality,
                'city' => 'Bareilly',
                'state' => 'Uttar Pradesh',
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => Str::limit($data['title'].' | Buraaqh Properties', 255, ''),
                'meta_description' => Str::limit($data['excerpt'], 155, ''),
                'focus_keyword' => $data['keyword'],
                'sort_order' => array_search($locality, array_keys($guides), true) + 1,
            ];

            if (! $guide) {
                NeighborhoodGuide::query()->create($attributes);
                continue;
            }

            if (trim((string) $guide->content) === $legacyContent($locality)) {
                unset($attributes['status'], $attributes['published_at'], $attributes['sort_order']);
                $guide->update($attributes);
            }
        }
    }
}
