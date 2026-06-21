<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $application_id
 * @property int|null $required_document_id
 * @property string $document_name
 * @property string $file_path
 * @property string $file_original_name
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ServiceApplication $application
 * @property-read \App\Models\RequiredDocument|null $requiredDocument
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereFileOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereRequiredDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationDocument whereUpdatedAt($value)
 */
	class ApplicationDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int $office_id
 * @property string|null $notes
 * @property string|null $uploaded_document
 * @property string $status
 * @property string|null $certificate_path
 * @property string|null $payment_method
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CryptoTransaction> $cryptoTransactions
 * @property-read int|null $crypto_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LocalPayment> $localPayments
 * @property-read int|null $local_payments_count
 * @property-read \App\Models\Office $office
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\ServiceApplication|null $serviceApplication
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereCertificatePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereUploadedDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CitizenRequest whereUserId($value)
 */
	class CitizenRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $citizen_request_id
 * @property string $currency
 * @property numeric $amount_usd
 * @property numeric $amount_crypto
 * @property numeric $crypto_price_usd
 * @property string $wallet_address
 * @property string|null $tx_hash
 * @property string $status
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CitizenRequest $citizenRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereAmountCrypto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereAmountUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereCitizenRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereCryptoPriceUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereTxHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CryptoTransaction whereWalletAddress($value)
 */
	class CryptoTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $citizen_request_id
 * @property string $method
 * @property numeric $amount_usd
 * @property string $account_details
 * @property string|null $reference_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CitizenRequest $citizenRequest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereAccountDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereAmountUsd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereCitizenRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LocalPayment whereUpdatedAt($value)
 */
	class LocalPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $service_request_id
 * @property int $user_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ServiceRequest $serviceRequest
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUserId($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $region
 * @property string|null $postal_code
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Office> $offices
 * @property-read int|null $offices_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereUpdatedAt($value)
 */
	class Municipality extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $description
 * @property string|null $address
 * @property string|null $city
 * @property string|null $phone
 * @property string|null $email
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property int|null $municipality_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Revenue> $revenues
 * @property-read int|null $revenues_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereUpdatedAt($value)
 */
	class Office extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $service_request_id
 * @property int $user_id
 * @property int $stars
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ServiceRequest $serviceRequest
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereStars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rating whereUserId($value)
 */
	class Rating extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $service_id
 * @property string $name
 * @property string|null $notes
 * @property bool $is_mandatory
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereIsMandatory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequiredDocument whereUpdatedAt($value)
 */
	class RequiredDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $office_id
 * @property numeric $amount
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Office $office
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Revenue whereUpdatedAt($value)
 */
	class Revenue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property numeric $price
 * @property string $currency
 * @property int $processing_days
 * @property int $office_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Office $office
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequiredDocument> $requiredDocuments
 * @property-read int|null $required_documents_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereProcessingDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereUpdatedAt($value)
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $reference_number
 * @property int $user_id
 * @property int $service_id
 * @property int $office_id
 * @property string $full_name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string|null $notes
 * @property numeric|null $citizen_lat
 * @property numeric|null $citizen_lng
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $citizen_request_id
 * @property string|null $certificate_path
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CitizenRequest|null $citizenRequest
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationDocument> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\Office $office
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereCertificatePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereCitizenLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereCitizenLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereCitizenRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceApplication whereUserId($value)
 */
	class ServiceApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property string $tracking_code
 * @property string|null $title
 * @property string|null $document_type
 * @property string|null $staff_notes
 * @property int $user_id
 * @property string $status
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Rating|null $rating
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereStaffNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereTrackingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereUuid($value)
 */
	class ServiceRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $reset_code
 * @property \Illuminate\Support\Carbon|null $reset_code_expires_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $id_document_path
 * @property string|null $id_document_type
 * @property string $id_verification_status
 * @property string $role
 * @property int|null $municipality_id
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_email_code
 * @property \Illuminate\Support\Carbon|null $two_factor_code_expires_at
 * @property bool $two_factor_enabled
 * @property string $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $office_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CitizenRequest> $citizenRequests
 * @property-read int|null $citizen_requests_count
 * @property-read string $name
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdVerificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereResetCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereResetCodeExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorCodeExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEmailCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

