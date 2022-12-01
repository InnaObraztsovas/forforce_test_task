use users;
# Баланс по каждому пользователю (сумма денег по всем номерам и операторам каждого пользователя)
select sum(p.balance) as 'total balance', u.name from phone as p join user u on p.user_id = u.id group by user_id;

# количество номеров телефонов по операторам (список: код оператора, кол-во номеров этого оператора);
select operator_code, count(*) as 'total' from phone group by operator_code;

# количество телефонов у каждого пользователя (список: имя пользователя, кол-во номеров у пользователя);\

select u.name, count(p.id) from user as u join phone p on u.id = p.user_id group by user_id;

# вывести имена 10 пользователей с максимальным балансом на счету (максимальный баланс по одному номеру);

select u.name from user as u join phone p on u.id = p.user_id order by p.balance desc limit 10;