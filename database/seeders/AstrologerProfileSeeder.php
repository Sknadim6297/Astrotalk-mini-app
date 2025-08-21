<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Astrologer;
use App\Models\Review;

class AstrologerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing astrologers
        $astrologers = Astrologer::with('user')->get();

        foreach ($astrologers as $astrologer) {
            // Update astrologer profile with available data
            $astrologer->update([
                'specialization' => $this->getRandomSpecializations(),
                'languages' => $this->getRandomLanguages(),
                'experience' => rand(3, 25),
                'per_minute_rate' => rand(20, 50)
            ]);

            // Create some sample reviews for each astrologer
            $reviewCount = rand(5, 15);
            $users = User::where('role', 'user')->take($reviewCount)->get();
            
            foreach ($users as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'astrologer_id' => $astrologer->id, // Use astrologer's ID, not user_id
                    'rating' => rand(4, 5), // High ratings for sample data
                    'comment' => $this->getRandomReviewComment(),
                    'is_verified' => rand(0, 1)
                ]);
            }
        }
    }

    private function getRandomBio()
    {
        $bios = [
            "Expert in Vedic astrology with years of experience in providing accurate predictions and spiritual guidance. Specializes in relationship counseling, career guidance, and life path analysis.",
            "Certified astrologer and numerologist helping clients understand their destiny through ancient wisdom. Known for precise predictions and practical solutions.",
            "Renowned astrologer with expertise in palmistry, face reading, and horoscope analysis. Provides personalized consultations for all life matters.",
            "Spiritual guide and astrologer with deep knowledge of Vedic scriptures. Helps clients find clarity in relationships, career, and personal growth.",
            "Professional astrologer specializing in business predictions, financial guidance, and career counseling. Uses scientific approach to astrology.",
            "Traditional astrologer with expertise in gemstone therapy, vastu consultation, and remedial measures. Focuses on holistic healing and guidance."
        ];
        return $bios[array_rand($bios)];
    }

    private function getRandomSpecializations()
    {
        $specializations = [
            'Vedic Astrology', 'Numerology', 'Tarot Reading', 'Palmistry', 'Face Reading',
            'Vastu Shastra', 'Gemstone Therapy', 'Love & Relationships', 'Career & Finance',
            'Health & Wellness', 'Spiritual Guidance', 'Marriage Compatibility', 'Business Astrology'
        ];
        
        shuffle($specializations);
        return array_slice($specializations, 0, rand(3, 6));
    }

    private function getRandomLanguages()
    {
        $languages = [
            'Hindi', 'English', 'Bengali', 'Tamil', 'Telugu', 'Gujarati', 
            'Marathi', 'Punjabi', 'Kannada', 'Malayalam', 'Sanskrit'
        ];
        
        shuffle($languages);
        return array_slice($languages, 0, rand(2, 4));
    }

    private function getRandomEducation()
    {
        $educations = [
            "PhD in Astrology from Banaras Hindu University",
            "Master's in Vedic Studies, Sanskrit University",
            "Jyotish Acharya from Indian Council of Astrological Sciences",
            "Bachelor's in Astronomy and Astrology",
            "Certified from All India Federation of Astrologers' Societies",
            "Traditional Guru-Shishya training in Vedic Astrology"
        ];
        return $educations[array_rand($educations)];
    }

    private function getRandomCertifications()
    {
        $certifications = [
            "Certified Vedic Astrologer (CVA)",
            "International Association of Astrologers Certificate",
            "Gemstone Therapy Specialist Certification",
            "Vastu Shastra Expert Certification",
            "Numerology Master Practitioner",
            "Traditional Palmistry Certification"
        ];
        return $certifications[array_rand($certifications)];
    }

    private function getRandomAvailability()
    {
        return [
            'monday' => ['09:00-12:00', '14:00-18:00'],
            'tuesday' => ['09:00-12:00', '14:00-18:00'],
            'wednesday' => ['09:00-12:00', '14:00-18:00'],
            'thursday' => ['09:00-12:00', '14:00-18:00'],
            'friday' => ['09:00-12:00', '14:00-18:00'],
            'saturday' => ['10:00-16:00'],
            'sunday' => ['10:00-14:00']
        ];
    }

    private function getRandomReviewComment()
    {
        $comments = [
            "Amazing accuracy in predictions! Really helped me make important life decisions.",
            "Very insightful session. The astrologer provided practical solutions and guidance.",
            "Detailed analysis and patient explanations. Highly recommended!",
            "Accurate predictions about my career. Very satisfied with the consultation.",
            "Great experience! The astrologer was very understanding and helpful.",
            "Precise timing predictions. Everything happened as predicted!",
            "Excellent guidance for relationship matters. Very grateful!",
            "Professional approach and deep knowledge. Will consult again.",
            "Helpful remedies and solutions. Noticed positive changes already.",
            "Clear explanations and practical advice. Worth every minute!"
        ];
        return $comments[array_rand($comments)];
    }
}
