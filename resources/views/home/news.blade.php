@php
setlocale(LC_TIME, 'ru_RU');
@endphp
<section id="news">
    <div class="news-wrapper">
        <h2 class="news-title tt tt-b t-32 tt-b-32-auto">Последние новости</h2>
        <div class="news-grid">
            @foreach ($news->take(12) as $news_instance)
                <a href="{{ route('news/get', ['slug' => $news_instance->slug]) }}" class="news-card">
                    @include('home.atoms.picture', [
                        'file_id' => $news_instance->thumbnail->id,
                        'attr' => 'width=340 height=238',
                        'class' => 'news-cover',
                        'download' => false,
                    ])
                    <div class="news-info">
                        <div class="card-misc">
                            <span class="card-date tx tx-r t-12 tx-r-12-auto">
                                {{ $news_instance->format_date('published') ?: $news_instance->format_date('updated') }}
                            </span>

                            @if ($news_instance->category)
                                <span class="card-category tx tx-r t-12 tx-r-12-auto">
                                    {{ $news_instance->category->content_title }}
                                </span>
                            @endif
                        </div>
                        <span
                            class="card-title tx tx-d t-16 tx-d-16-subtitle">{{ $news_instance->content_title }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
