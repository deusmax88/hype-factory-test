create schema if not exists test collate utf8mb4_general_ci;

create table if not exists news_category
(
	id int auto_increment
		primary key,
	title varchar(255) not null
)
charset=utf8mb4;

create table if not exists news
(
	id int auto_increment
		primary key,
	category_id int null,
	title varchar(255) not null,
	text text null,
	constraint news_news_category_id_fk
		foreign key (category_id) references news_category (id)
			on update cascade on delete set null
)
charset=utf8mb4;

create index news_category_id_index
	on news (category_id);

create table if not exists news_post
(
	id int auto_increment
		primary key,
	news_id int not null,
	content varbinary(243) not null,
	constraint news_post_news_id_fk
		foreign key (news_id) references news (id)
			on update cascade on delete cascade
);

create index news_post_news_id_index
    on news_post (news_id);

create table if not exists user
(
	id int auto_increment
		primary key,
	nickname varchar(255) not null
)
charset=utf8mb4;

create table if not exists post_like
(
	post_id int not null,
	user_id int not null,
	primary key (post_id, user_id),
	constraint post_like_news_post_id_fk
		foreign key (post_id) references news_post (id)
			on update cascade on delete cascade,
	constraint post_like_user_id_fk
		foreign key (user_id) references user (id)
			on update cascade on delete cascade
);

create index post_like_user_id_post_id_index
	on post_like (user_id, post_id);

-- Запрос для выборки контента по категории
SELECT
    n.id,
    n.title,
    n.text,
    np.content,
    pl.num_of_likes

FROM
    news n
    LEFT JOIN news_post np ON np.news_id = n.id
    LEFT JOIN (
        SELECT post_id, count(post_id) as num_of_likes
        FROM
            post_like
        GROUP BY
            post_id
    ) as pl ON pl.post_id = np.id
WHERE
    category_id IN (1)

-- Просмотр всех пользователей лайкнувших пост
SELECT
    u.nickname

FROM
    post_like pl
    LEFT JOIN
        user u ON pl.user_id = u.id
WHERE
    pl.post_id = 2