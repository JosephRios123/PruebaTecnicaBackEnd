create database pruebatecnica;

use pruebatecnica;

create table reserves (
    id int auto_increment primary key,
    qty_passengers int not null,
    adult int not null,
    child int default null,
    baby int default null,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp
);

create table itineraries (
    id int auto_increment primary key,
    reserve_id int not null,
    departure_city varchar(3) not null,
    arrival_city varchar(3) not null,
    departure_hour timestamp not null,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    foreign key (reserve_id) references reserves(id) on delete cascade
);


select * from reserves;
select * from itineraries;