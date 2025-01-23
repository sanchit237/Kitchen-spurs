<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ArticleSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;

    /**
     * Create a new job instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('openai.api_key'),
            ])->withoutVerifying()->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that generates SEO-friendly slugs.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a short, unique, SEO-friendly slug for the following article based on combination of its title and content:\n\n"
                            . "Title: {$this->article->title}\n\n"
                            . "Content: {$this->article->content}\n\n"
                            . "The slug should be concise, descriptive, and lowercase, with words separated by hyphens."
                    ]
                ],
                'max_tokens' => 20,
                'temperature' => 0.5,
            ]);

            // Logging the response
            Log::info('OpenAI API response:', ['response' => $response->json()]);

            // processing the response
            $data = $response->json();
            $slug = $data['choices'][0]['message']['content'] ?? null;

            // update the slug
            $this->article->update(['slug' => $slug]);
        }
        catch (Exception $e) {
            Log::error("Failed to generate slug for Article ID {$this->article->id}: " . $e->getMessage());
        }
    }
}
