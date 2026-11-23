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
        $this->seedFaqs();
        $this->seedNeighborhoodGuides();
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                'question' => 'What should I check before buying a property in Bareilly?',
                'old_answer' => 'Review the ownership chain, approved plans where applicable, land-use status, encumbrances, taxes and the terms of the sale agreement. Have the documents independently checked by a qualified local professional.',
                'answer' => 'Start with the exact property address and the seller’s authority to transfer it. Review the registered ownership documents and relevant prior deeds, land-use information, sanctioned plans where applicable, tax and maintenance dues, and any mortgage, charge or dispute. Inspect the property and its access, utilities and visible condition. Have a qualified local property lawyer review original records and verify relevant details with the competent authorities. The documents required differ for a plot, apartment, independent house and commercial premises, so use a property-specific checklist rather than relying on a generic list.',
                'category' => 'Buying',
            ],
            [
                'question' => 'How can I compare two residential properties?',
                'old_answer' => 'Compare the exact location, usable area, construction condition, access, utilities, title documents, total transaction cost and resale considerations. Visit each property and verify all seller-provided information.',
                'answer' => 'Use the same checklist for both properties. Compare the exact street and approach, usable layout, measured area, construction condition, daylight and ventilation, parking, water and electricity arrangements, drainage, maintenance obligations and total acquisition cost. Review the ownership and approval documents for each property independently. Visit both at different times where possible and record unanswered questions. Do not compare only the asking price or a locality name; a lower price may reflect a difference in condition, access, legal status or included facilities.',
                'category' => 'Buying',
            ],
            [
                'question' => 'What documents are commonly reviewed during a property purchase?',
                'old_answer' => 'Depending on the property and transaction, buyers commonly review title documents, prior sale deeds, sanctioned plans where applicable, tax receipts, identity documents and applicable project or authority approvals. Requirements vary; obtain professional advice.',
                'answer' => 'The checklist depends on the property and its history. It may include the current registered deed, relevant prior ownership documents, seller identity and authority, revenue or municipal records, land-use information, sanctioned plans, completion or occupancy records where applicable, tax receipts, lender release documents and project disclosures. For a project covered by RERA, check the relevant official registration and disclosures. Ask a qualified property lawyer to identify the records required for the exact transaction and verify originals with the appropriate authorities.',
                'category' => 'Documentation',
            ],
            [
                'question' => 'Should I pay a booking amount before document verification?',
                'old_answer' => 'Do not rely on verbal assurances. Understand the written booking terms, refund conditions, seller authority and relevant documents before making a payment. Seek independent legal advice when needed.',
                'answer' => 'Do not transfer money simply because a property is described as urgent or in demand. First confirm the identity and authority of the recipient, the exact property, the written booking terms, refund conditions, payment schedule and any conditions that must be met. Ask which documents have been checked and which remain outstanding. Have the terms reviewed independently where appropriate, pay only through a traceable agreed method and retain a receipt. A verbal promise does not replace a written agreement or title review.',
                'category' => 'Payments',
            ],
            [
                'question' => 'What affects residential property value in Bareilly?',
                'old_answer' => 'Value can depend on micro-location, road access, plot or built-up area, construction quality, legal status, available infrastructure and comparable transactions. Verify current market evidence rather than relying on general estimates.',
                'answer' => 'A property’s price may be influenced by its exact street, access, plot or usable area, layout, construction condition, permitted use, ownership records, utility arrangements, maintenance costs and the terms of comparable transactions. Conditions can vary within one locality. Compare properties with similar size, age, condition and legal status, and distinguish asking prices from completed transaction evidence. No general locality description can establish a current market value or guarantee future appreciation; obtain a qualified valuation where a formal opinion is needed.',
                'category' => 'Property value',
            ],
            [
                'question' => 'Can Buraaqh Properties confirm legal title or government approvals?',
                'old_answer' => 'A property decision should be supported by independent document verification and relevant authority records. Do not treat marketing information as a legal opinion or an official approval.',
                'answer' => 'Website content and general property discussions are not a legal title opinion or an official approval. Verify the registered ownership documents, land-use status, building permissions and other applicable records with the relevant authority, and ask a qualified property lawyer to review the originals. The required checks depend on the exact property and transaction. Do not make a payment or sign an agreement on the assumption that a listing description certifies title or compliance.',
                'category' => 'Documentation',
            ],
            [
                'question' => 'How can I confirm whether a property listing is still available?',
                'answer' => 'Ask the person or business responsible for the listing to confirm the date on which availability was last checked, the exact address, the current asking terms and whether any offer or agreement is already in progress. A website page can remain online after circumstances change. Treat availability as unconfirmed until you receive a current response and verify the details directly with the authorised owner or representative.',
                'category' => 'Listings',
            ],
            [
                'question' => 'What should I inspect during a property site visit?',
                'answer' => 'Check the exact address and access, room layout, measured area, visible cracks or dampness, roof and ceiling condition, doors and windows, plumbing, electrical fittings, ventilation, water supply, drainage, parking and shared spaces. Ask about repairs, occupancy, maintenance charges and utility arrangements. For technical or structural questions, appoint an independent qualified inspector; a viewing checklist cannot certify that a building is safe or defect-free.',
                'category' => 'Site visits',
            ],
            [
                'question' => 'Are advertised asking prices the same as market value?',
                'answer' => 'No. An asking price is the amount a seller or advertiser is requesting; it is not proof of the amount a buyer will pay or a completed transaction value. Compare genuinely similar properties and note differences in location, size, condition, approvals and transaction date. Where a formal valuation is required for lending, tax, accounting or another purpose, appoint a suitably qualified valuer.',
                'category' => 'Property value',
            ],
            [
                'question' => 'What should I check before choosing a commercial property?',
                'answer' => 'Start with the business activity the premises must support. Verify that the specific use is permitted at the address and review access, customer movement, parking, loading, signage, power requirements, fit-out permissions, maintenance charges and operating costs. Check ownership, building approvals, occupancy arrangements and written lease or sale terms. Do not assume that a property advertised as commercial can be used for every type of business.',
                'category' => 'Commercial',
            ],
            [
                'question' => 'What is mutation and does it prove ownership?',
                'answer' => 'Mutation is an administrative update to relevant revenue or municipal records after a change that the authority recognises. Its purpose and procedure depend on the record and property type. A mutation entry or tax receipt should not be treated on its own as conclusive proof of legal title. Ask a qualified property lawyer to review the registered documents and explain which records should be updated after the transaction.',
                'category' => 'Documentation',
            ],
            [
                'question' => 'How should I check a real estate project under RERA?',
                'answer' => 'If the project or phase is covered by the Real Estate (Regulation and Development) Act, check the relevant official Uttar Pradesh RERA record and compare the registered project name, promoter, phase, disclosures and unit information with the documents supplied to you. Confirm whether the specific transaction falls within the applicable requirements. RERA information is one part of due diligence and does not replace independent title, plan and agreement review.',
                'category' => 'Documentation',
            ],
            [
                'question' => 'Can a real estate consultant guarantee appreciation or rental income?',
                'answer' => 'No responsible consultant can guarantee future property prices, rental income, occupancy, resale timing, loan approval or a completed transaction. These outcomes depend on market conditions, property-specific facts and decisions made by other parties. Ask for the source and date of any market information, understand the assumptions and make your own decision after independent legal and financial review.',
                'category' => 'Consultation',
            ],
            [
                'question' => 'What information should I include in a property enquiry?',
                'answer' => 'Include the property type, preferred locality, intended use, approximate budget range, size or layout needs and your expected timeline. If you are asking about a published listing, include its link or reference. Share only contact information needed for a response; do not submit passwords, bank details, payment credentials or unnecessary identity-document numbers through a general enquiry form.',
                'category' => 'Enquiries',
            ],
            [
                'question' => 'How should I plan the total budget for a property purchase?',
                'answer' => 'Consider more than the advertised price. Include applicable stamp duty and registration charges, legal review, loan processing, brokerage if agreed, taxes, society or maintenance dues, utility connections, repairs, moving and furnishing. Ask for a written breakdown and confirm current government charges from official sources at the time of the transaction. Keep a contingency for costs that become clear after inspection.',
                'category' => 'Payments',
            ],
            [
                'question' => 'How can I arrange a property site visit?',
                'answer' => 'Share the listing reference or property details and your preferred days through the enquiry form. Before travelling, confirm that the property is currently available, the exact address, access arrangements and the identity of the person authorised to show it. A site visit should not be treated as a document check or technical inspection; arrange those separately where needed.',
                'category' => 'Site visits',
            ],
        ];

        foreach ($faqs as $index => $faqData) {
            $faq = FaqItem::query()->firstOrNew(['question' => $faqData['question']]);
            $oldAnswer = $faqData['old_answer'] ?? null;
            unset($faqData['old_answer']);

            if (! $faq->exists) {
                $faq->fill($faqData + [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])->save();

                continue;
            }

            if ($oldAnswer !== null && $faq->answer === $oldAnswer) {
                $faq->forceFill(['answer' => $faqData['answer'], 'category' => $faqData['category']])->save();
            }
        }
    }

    private function seedNeighborhoodGuides(): void
    {
        $guides = [
            [
                'locality' => 'Civil Lines',
                'slug' => 'civil-lines-bareilly-property-guide',
                'title' => 'Civil Lines, Bareilly: A Property Evaluation Guide',
                'excerpt' => 'A street-level checklist for comparing access, building use, parking, maintenance and documents when considering property in Civil Lines.',
                'focus_keyword' => 'Civil Lines Bareilly property guide',
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
            [
                'locality' => 'Rajendra Nagar',
                'slug' => 'rajendra-nagar-bareilly-property-guide',
                'title' => 'Rajendra Nagar, Bareilly: A Residential Property Checklist',
                'excerpt' => 'Questions to ask about everyday convenience, street conditions, building maintenance and ownership records when comparing homes in Rajendra Nagar.',
                'focus_keyword' => 'Rajendra Nagar Bareilly property',
                'content' => <<<'HTML'
<p>This guide helps buyers assess an individual home in Rajendra Nagar, Bareilly. It does not make a claim about current prices, infrastructure, transport links or the availability of any particular facility. A locality name cannot establish the condition or suitability of a specific street, building or plot.</p>
<h2>Match the location to your routine</h2>
<p>Write down the places your household visits regularly and test the actual journeys from the property entrance. Check the route at the times you expect to travel. Consider walking conditions, crossings, lighting, parking and whether the route is practical for children, older family members or anyone with mobility needs.</p>
<h2>Inspect the home and shared spaces</h2>
<p>Look at the roof, ceilings, walls, floors, windows, ventilation, plumbing, electrical fittings and visible signs of dampness. Ask about repairs and maintenance history. If you are considering an apartment or floor, inspect stairways, lifts where relevant, parking, water storage, drainage and the upkeep of common areas.</p>
<h2>Verify utility arrangements</h2>
<p>Ask how water, electricity, sewage, waste collection and internet services are provided to the exact property. Confirm whether any connection is shared and how charges are divided. Request current bills or records where appropriate. Do not assume that arrangements at one building are the same for another property nearby.</p>
<h2>Review documents and responsibilities</h2>
<p>Check the seller's authority, registered ownership records, property description, boundaries, applicable building permissions, taxes, dues and any mortgage or dispute. Ask about society or maintenance rules, parking rights and any outstanding repair obligations. A qualified local lawyer should review original documents and confirm which authority records apply.</p>
<h2>Compare options consistently</h2>
<p>Use the same checklist for each property and record the visit date, exact address, area, condition, recurring costs and unresolved questions. Compare evidence rather than relying on a general impression or an advertised price. If a future facility or development is mentioned, verify it through an official source before treating it as part of the decision.</p>
HTML,
            ],
            [
                'locality' => 'Izatnagar',
                'slug' => 'izatnagar-bareilly-property-guide',
                'title' => 'Izatnagar, Bareilly: Questions to Ask Before Buying',
                'excerpt' => 'A practical framework for checking exact-site access, noise, utilities, land status and building documents when assessing property in Izatnagar.',
                'focus_keyword' => 'Izatnagar Bareilly property guide',
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
            [
                'locality' => 'Pilibhit Bypass',
                'slug' => 'pilibhit-bypass-bareilly-property-guide',
                'title' => 'Pilibhit Bypass, Bareilly: A Property Access and Due-Diligence Guide',
                'excerpt' => 'How to assess approach roads, entry points, daily travel, services and property records for a specific address near Pilibhit Bypass.',
                'focus_keyword' => 'Pilibhit Bypass Bareilly property',
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
            [
                'locality' => 'DD Puram',
                'slug' => 'dd-puram-bareilly-property-guide',
                'title' => 'DD Puram, Bareilly: A Checklist for Comparing Homes',
                'excerpt' => 'A practical checklist for comparing the exact block, building condition, parking, utilities, maintenance and documents in DD Puram.',
                'focus_keyword' => 'DD Puram Bareilly property guide',
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
            [
                'locality' => 'Bareilly Cantt',
                'slug' => 'bareilly-cantt-property-guide',
                'title' => 'Bareilly Cantt: Property Permission and Document Checks',
                'excerpt' => 'Additional questions to raise about jurisdiction, land status, access and permissions when assessing a property in or around Bareilly Cantt.',
                'focus_keyword' => 'Bareilly Cantt property due diligence',
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
            [
                'locality' => 'Mahanagar',
                'slug' => 'mahanagar-bareilly-property-guide',
                'title' => 'Mahanagar, Bareilly: A Guide to Property Selection',
                'excerpt' => 'Questions about street conditions, permitted use, utilities, maintenance and ownership records to ask when comparing property in Mahanagar.',
                'focus_keyword' => 'Mahanagar Bareilly property guide',
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

        foreach ($guides as $index => $guideData) {
            $guide = NeighborhoodGuide::query()->firstOrNew(['slug' => $guideData['slug']]);
            $legacyContent = '<p>Use this guide as a starting point when assessing a property in '.$guideData['locality'].'. It is not a rating of the locality or a statement that any particular facility, road, school or project is nearby.</p><h2>What to verify on a visit</h2><ul><li>Check the exact street, approach road and access at different times of day.</li><li>Confirm water, electricity, drainage and other utility arrangements for the specific property.</li><li>Verify land use, title chain, sanctioned plans and applicable approvals independently.</li><li>Compare recent transactions for genuinely comparable properties in the immediate area.</li><li>Check the written terms, total charges and any society or maintenance obligations.</li></ul><p>Local conditions can vary street by street. Confirm current details with relevant authorities and qualified local professionals before making a decision.</p>';

            if (! $guide->exists) {
                $guide->fill($guideData + [
                    'city' => 'Bareilly',
                    'state' => 'Uttar Pradesh',
                    'status' => 'published',
                    'published_at' => now()->subDays($index),
                    'sort_order' => $index + 1,
                    'meta_title' => Str::limit($guideData['title'].' | Buraaqh Properties', 255, ''),
                    'meta_description' => Str::limit($guideData['excerpt'], 155, ''),
                ])->save();

                continue;
            }

            if ($guide->content === $legacyContent) {
                $guide->forceFill([
                    'title' => $guideData['title'],
                    'excerpt' => $guideData['excerpt'],
                    'content' => $guideData['content'],
                    'meta_title' => Str::limit($guideData['title'].' | Buraaqh Properties', 255, ''),
                    'meta_description' => Str::limit($guideData['excerpt'], 155, ''),
                    'focus_keyword' => $guideData['focus_keyword'],
                ])->save();
            }
        }
    }
}
