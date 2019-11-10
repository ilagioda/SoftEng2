# USE CASE - STORY#5

**Use Case**: 
Enter the class composition in the system

**Scope:** 
Web browser

**Level:** 
User-goal

**Intention in context:** 
The administrative officer wants to enter class compositions into the system.

**Primary actor:** 
Administrative officer(admin).

**Secondary/Support actor(s):**
Principal: has to upload the proposed class compositions.

**Stakeholders' Interests:**
- Parent: would like to see in which class his/her child was inserted.
- Teacher: wants to see the composition of a particular class.
- Administrative officer: wants to enter class compositions into the system before the beginning of the school year.
- Principal: would like to see if the class compositions proposed were accepted or not.

**Precondition:**
- The system must have a database already configured
- The system should already have the proposed class compositions
- The students must be enrolled in the school.
- The admin must be registered on the website
- The admin must be logged on the website

**Minimum Guarantees:** 
- The class composition is accepted and the information saved
- Nobody except the admin is able to enter the class composition into the system 

**Success Guarantees:** 
- The administrative officer enters the class compositions
-The students once the class composition is accepted must be enrolled in that particular class

**Trigger:** 
None, because it’s the administrative officer that starts the interaction

**Main Success Scenario:**
1. The admin selects a particular class for which he/she wants to see the proposed class composition
2. The system shows the proposed class composition for the selected class
3. The admin accepts the class composition by simply clicking a button 
4. The system updates all the informations related in the database
5. The system confirms with a message that the operation has been completed successfully 

The use case terminates with success

**Extensions:**
1a. The admin is inactive for more than threshold seconds: the admin is redirected to the login page.
2a. The database is not reachable: the use case terminates in failure 
3a. The admin doesn't accept the class composition: the system must not change the class compositions and the info related to the students 
3b. The admin has selected the wrong class: the system provides a way to go back to the first choice of the class



