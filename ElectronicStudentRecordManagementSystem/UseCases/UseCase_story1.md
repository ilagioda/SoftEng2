# USE CASE - STORY#1

##### Use Case: 
View the marks of their child.

##### Scope: 
Parent pc/smartphone.

##### Level: 
User-goal.

##### Intention in context: 
A parent wants to view all the marks assigned to their child during the current semester.

##### Primary actor: 
Parent.

##### Support Actors: 
Teacher: has to upload the marks of the child.

##### Stakeholders' Interests:
- Parent: wants to see the marks of his/her child so that he/she can monitor his/her school performance.
- Teacher: wants the parents to see the marks of their child, so that they can take provisions if needed.
- Principal: wants the parents to see the marks of their child, to offer the best possible service and keep them informed.

##### Precondition:
- The student must be enrolled in the school.
- The parent must have sent the request to have access to the website to the admin.
- The admin must have allowed the parent to access to the website, having sent to him/her the credentials.
- The parent must be registered on the website.
- The parent must be logged in on the website.


##### Minimum Guarantees: 
The marks of a child can not be seen by an unauthorised person ( anyone who is not either one of their parents/tutors or one of its teachers).

##### Success Guarantees: 
The parent sees the marks of their child.

##### Main Success Scenario:
1. Parent selects one of his/her child enrolled in the school.
2. System retrieves and show data about him/her.
3. Parent asks to view the marks of the selected child.
4. System shows the marks or N.C. if no marks for a given subject.

The use case terminates with success.

#### Extensions:

**1a.** The parent is inactive for more than threshold seconds.
The parent is redirected to the login page.

**1b.** The parent has just one child.
    The scenario starts at step 3.
    The use case terminates with success.

**1bb.** The scenario starts at step 3.
The parent is inactive.
The parent is redirected to the login phase

**1bc.** The scenario starts at step 3.
The database is not reachable.
The system shows an error message and asks to try later.

**2a.** The database is not reachable.
    The system shows an error message and asks to try later.

**3a.** The parent is inactive for more than threshold seconds.
    The parent is redirected to the login page.

**4a.** The database is not reachable.
    The system shows an error message and asks to try later.




