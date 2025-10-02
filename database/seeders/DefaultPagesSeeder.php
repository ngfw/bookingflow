<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class DefaultPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Homepage
        $homepage = Page::create([
            'title' => 'Welcome to Our Beauty Salon',
            'slug' => 'home',
            'excerpt' => 'Experience luxury beauty services in a relaxing environment. Our professional team is dedicated to making you look and feel your absolute best.',
            'content' => '',
            'template' => 'homepage',
            'is_published' => true,
            'is_homepage' => true,
            'published_at' => now(),
        ]);

        // Add sections to homepage
        PageSection::create([
            'page_id' => $homepage->id,
            'section_type' => 'hero',
            'title' => 'Welcome to Our Beauty Salon',
            'content' => 'Experience luxury beauty services in a relaxing environment. Our professional team is dedicated to making you look and feel your absolute best.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $homepage->id,
            'section_type' => 'services',
            'title' => 'Our Services',
            'content' => 'Professional beauty services tailored to your needs',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $homepage->id,
            'section_type' => 'gallery',
            'title' => 'Our Work',
            'content' => 'See the beautiful transformations we create',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $homepage->id,
            'section_type' => 'testimonials',
            'title' => 'What Our Clients Say',
            'content' => 'Real experiences from our satisfied customers',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Create About Page
        $aboutPage = Page::create([
            'title' => 'About Us',
            'slug' => 'about',
            'excerpt' => 'Learn about our salon, our team, and our commitment to providing exceptional beauty services.',
            'content' => '<p>Welcome to our beauty salon, where we believe that everyone deserves to look and feel their absolute best. With years of experience in the beauty industry, our team of skilled professionals is dedicated to providing you with exceptional services in a relaxing and welcoming environment.</p><p>We pride ourselves on using only the highest quality products and staying up-to-date with the latest beauty trends and techniques. Our goal is to help you discover your natural beauty and boost your confidence.</p>',
            'template' => 'default',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Add team section to about page
        PageSection::create([
            'page_id' => $aboutPage->id,
            'section_type' => 'team',
            'title' => 'Meet Our Team',
            'content' => 'Our skilled professionals are here to help you look and feel your best',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Contact Page
        $contactPage = Page::create([
            'title' => 'Contact Us',
            'slug' => 'contact',
            'excerpt' => 'Get in touch with us to book your appointment or ask any questions.',
            'content' => '',
            'template' => 'contact',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Add contact section
        PageSection::create([
            'page_id' => $contactPage->id,
            'section_type' => 'contact',
            'title' => 'Get In Touch',
            'content' => 'Get in touch with us to book your appointment or ask any questions',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Services Page
        $servicesPage = Page::create([
            'title' => 'Our Services',
            'slug' => 'services',
            'excerpt' => 'Discover our comprehensive range of beauty services designed to help you look and feel your best.',
            'content' => '',
            'template' => 'services',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Add services section
        PageSection::create([
            'page_id' => $servicesPage->id,
            'section_type' => 'services',
            'title' => 'Our Services',
            'content' => 'Professional beauty services tailored to your needs',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Gallery Page
        $galleryPage = Page::create([
            'title' => 'Gallery',
            'slug' => 'gallery',
            'excerpt' => 'Browse through our portfolio of beautiful transformations and see the amazing work we do.',
            'content' => '',
            'template' => 'gallery',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Add gallery section
        PageSection::create([
            'page_id' => $galleryPage->id,
            'section_type' => 'gallery',
            'title' => 'Our Work',
            'content' => 'See the beautiful transformations we create',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Blog Page
        $blogPage = Page::create([
            'title' => 'Blog',
            'slug' => 'blog',
            'excerpt' => 'Stay updated with beauty tips, trends, and salon news from our experts.',
            'content' => '',
            'template' => 'blog',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Add blog section
        PageSection::create([
            'page_id' => $blogPage->id,
            'section_type' => 'blog',
            'title' => 'Latest News & Tips',
            'content' => 'Stay updated with beauty tips, trends, and salon news',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Privacy Policy Page
        $privacyPage = Page::create([
            'title' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'excerpt' => 'Learn how we collect, use, and protect your personal information.',
            'content' => '<h2>Information We Collect</h2><p>We collect information you provide directly to us, such as when you create an account, book an appointment, or contact us.</p><h2>How We Use Your Information</h2><p>We use the information we collect to provide, maintain, and improve our services, process appointments, and communicate with you.</p><h2>Information Sharing</h2><p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent.</p><h2>Data Security</h2><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>',
            'template' => 'default',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);

        // Create Terms of Service Page
        $termsPage = Page::create([
            'title' => 'Terms of Service',
            'slug' => 'terms-of-service',
            'excerpt' => 'Read our terms and conditions for using our services.',
            'content' => '<h2>Acceptance of Terms</h2><p>By accessing and using our services, you accept and agree to be bound by the terms and provision of this agreement.</p><h2>Use License</h2><p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p><h2>Disclaimer</h2><p>The materials on our website are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties.</p><h2>Limitations</h2><p>In no event shall our company or its suppliers be liable for any damages arising out of the use or inability to use the materials on our website.</p>',
            'template' => 'default',
            'is_published' => true,
            'is_homepage' => false,
            'published_at' => now(),
        ]);
    }
}
