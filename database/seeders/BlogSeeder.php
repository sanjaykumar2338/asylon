<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Safety Insights',
            'School Security',
            'Church Safety',
            'Workplace Threats',
        ];

        $categoryMap = [];
        foreach ($categories as $name) {
            $categoryMap[$name] = BlogCategory::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name],
            );
        }

        $posts = [
            [
                'title' => 'How Anonymous Reporting Reduces Risk in Schools',
                'category' => 'School Security',
                'excerpt' => 'Anonymous channels surface early warning signs—bullying, weapons, mental health concerns—so teams can intervene sooner.',
                'content' => '<p>Schools often miss early indicators because students fear retaliation or do not trust traditional channels. Anonymous reporting lowers that barrier. Pair it with clear routing and documented responses so nothing falls through the cracks.</p><ul><li>Provide an easy, mobile-friendly form</li><li>Offer named and anonymous options</li><li>Route by category and urgency</li><li>Close the loop with documented follow-up</li></ul>',
                'featured_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
                'featured_image_alt' => 'Students walking at school',
            ],
            [
                'title' => 'Church Safety: Encouraging Quiet, Confidential Tips',
                'category' => 'Church Safety',
                'excerpt' => 'Members hesitate to speak up. Confidential, low-friction channels help leadership respond with care before issues escalate.',
                'content' => '<p>Concerns about harassment, disruptive visitors, or wellbeing issues often stay silent in faith communities. A discreet reporting workflow—with restricted access for pastoral or safety leads—builds trust and speeds response.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1473181488821-2d23949a045a?auto=format&fit=crop&w=1200&q=80',
                'featured_image_alt' => 'Church building exterior',
            ],
            [
                'title' => 'Workplace Threats: Capture Signals Before They Escalate',
                'category' => 'Workplace Threats',
                'excerpt' => 'Employees see early signs—aggression, retaliation, policy abuse. Anonymous intake with clear escalation keeps leadership ahead of risk.',
                'content' => '<p>Route reports to HR, security, or compliance based on category. Maintain an audit trail, evidence, and timelines so every action is documented. Publish the channel internally so staff know it is safe to speak up.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80',
                'featured_image_alt' => 'Modern office corridor',
            ],
        ];

        foreach ($posts as $post) {
            $category = $categoryMap[$post['category']] ?? null;

            BlogPost::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($post['title'])],
                [
                    'title' => $post['title'],
                    'category_id' => $category?->id,
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'featured_image' => $post['featured_image'],
                    'featured_image_alt' => $post['featured_image_alt'],
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                    'meta_title' => $post['title'].' | Asylon',
                    'meta_description' => $post['excerpt'],
                ]
            );
        }
    }
}
