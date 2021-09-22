# Bilkent University Scheduler

## What It Is
Our app accepts as many courses as a student may want to take along with all the offered sections of each course.  Then, with
our algorithm, generates all possible non-conflicting schedules.

## Where It Is
Right now the PHP version is available at https://scheduler.melihsunbul.com.

## Run Your Own
Although there are many customizations that are probably specific to only the University of Richmond, if you wanted to run your own here's how.
1. Clone this repository
2. Install dependencies using composer. `php composer.phar install` or `composer install`
3. Edit `config.php` with the correct installation directory and database login details
4. Run `getOfferings.php` from a web browser to create the database and import all the data
5. You now have the same installation that I do. 


## Theory of Operation
The new algorithm implemented by commit 9102865 is the Bron-Kerbosch maximal clique finding algorithm. I realized that the scheduling program could be thought of as a graph where vertices represent a section of a class and edges exist between vertices that are compatible (can be taken together).

Representing the problem as a graph means that possible non-conflicting schedules are maximal cliques. Therefore, to find all possible schedules, I implemented the Bron-Kerbosch maximal clique finding algorithm. This does run faster than my old algorithm and generates fewer total schedules because the old algorithm generated some schedules that were included in larger ones (sub-graphs). 

