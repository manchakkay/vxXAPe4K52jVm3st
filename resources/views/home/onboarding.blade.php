<section id="onboarding">
    <canvas></canvas>
    <div class="landing">
        <div class="lead">
            <div class="lead-text">
                <h1 class="tt tt-b t-64 tt-b-64-title">Направление твоего обучения</h1>
                <h2 class="tx tx-r t-16 tx-r-16-text">Инновационные методики обучения и высокие технологии в центре
                    Сибири</h2>
            </div>
            <div class="lead-buttons">
                <a href="#programs" class="b-big btn btn-tx-main tx tx-m t-16 tx-m-16-button">Программы обучения</a>
                <a href="#about" class="b-big btn btn-tx-ghost-white tx tx-m t-16 tx-m-16-button">О факультете</a>
            </div>
        </div>

        <div class="news-important">
            <div class="swiper">
                <div class="swiper-wrapper">
                    @if (count($important_news) != 0)
                        @foreach ($important_news->take(6) as $imp_news)
                            <div class="swiper-slide news-instance"
                                data-link="{{ route('news/get', ['slug' => $imp_news->news->slug]) }}">
                                @include('home.atoms.picture', [
                                    'file_id' => $imp_news->news->thumbnail->id,
                                    'attr' => 'width="500" height="350"',
                                    'class' => 'news-cover',
                                    'download' => false,
                                    'lazy' => false,
                                ])
                                {{-- <img loading="lazy" class="news-cover" width="500" height="350"
                                    src="{{ Storage::url($imp_news->news->thumbnail->converted('webp')) }}"> --}}
                                <div class="news-text">
                                    <span
                                        class="news-title tt tt-m t-24 tt-m-24-subtitle">{{ $imp_news->news->content_title }}</span>
                                    <span
                                        class="news-description tx tx-r t-16 tx-r-16-text">{{ $imp_news->news->content_description }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($news->take(6) as $news_instance)
                            <div class="swiper-slide news-instance"
                                data-link="{{ route('news/get', ['slug' => $news_instance->slug]) }}">
                                @include('home.atoms.picture', [
                                    'file_id' => $news_instance->thumbnail->id,
                                    'attr' => 'width=500 height=350',
                                    'class' => 'news-cover',
                                    'download' => false,
                                ])
                                <div class="news-text">
                                    <span
                                        class="news-title tt tt-m t-24 tt-m-24-subtitle">{{ $news_instance->content_title }}</span>
                                    <span
                                        class="news-description tx tx-r t-16 tx-r-16-text">{{ $news_instance->content_description }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
                <div class="news-controls">
                    <div class="news-buttons">
                        @if (count($important_news) != 0)
                            <a href="{{ route('news/get', ['slug' => $important_news[0]->news->slug]) }}"
                                class="news-btn-more b-med btn btn-tx-ghost-white tx tx-m t-14 tx-m-14-button">Подробнее</a>
                        @elseif (count($news) != 0)
                            <a href="{{ route('news/get', ['slug' => $news[0]->slug]) }}"
                                class="news-btn-more b-med btn btn-tx-ghost-white tx tx-m t-14 tx-m-14-button">Подробнее</a>
                        @endif

                        @if (count($important_news) != 0 || count($news) != 0)
                            <a href="#news"
                                class="news-btn-all b-med btn btn-tx-ghost-white tx tx-m t-14 tx-m-14-button">К
                                новостям</a>
                        @endif
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>
        </div>
    </div>
</section>
