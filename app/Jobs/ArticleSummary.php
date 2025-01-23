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

class ArticleSummary implements ShouldQueue
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
                        'content' => 'You are a professional assistant that generates concise and professional summaries for articles.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a concise, professional, 2-3 sentence summary for the following article based on its content:\n\n"
                            . "Content: {$this->article->content}\n\n"
                            . "Summary:"
                    ]
                ],
                'max_tokens' => 60,
                'temperature' => 0.5,
            ]);

            // Log the full response object
            Log::info('OpenAI API response:', ['response' => $response->json()]);

            // Decode and process the response
            $data = $response->json();
            $summary = $data['choices'][0]['message']['content'] ?? null;

            //update the summary
            $this->article->update(['summary' => $summary]);
        }
        catch (Exception $e) {
            Log::error("Failed to generate summary for Article ID {$this->article->id}: " . $e->getMessage());
        }
    }
}
