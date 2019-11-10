# USE CASE - STORY 3

**Use Case**: Enable access to parents

**Scope**: Admin's PC

**Level**: User-goal

**Intention in context**: The administrative officer has a list of parents contacts whose access is still to be enabled and can send a mail to the selected ones.

**Primary actor**: Administrative officer (admin)

**Secondary/Support actor(s)**: None

**Stakeholders' Interestes**: 

- _Parent_: Wants to access the system in order to monitor the student.
- _Teacher_: Needs that parents have access so that he/she can inform them about their child's school performance
- _Administrative Officer_: Needs to have an easy way to select a parent and grant him/her access.
- _Principal_: //

**Precondition**:

- Parent's email address should be already present in the Database and should have never accessed the website before.

**Minimum Guarantees**: None

**Success Guarantees**: 

- Parent will have an access to the website by exploiting the credentials present in the received mail and a new password is assigned to the corresponding entry in the Database.

**Trigger**:

- Parent asks for some credentials to access the website.

**Main Success Scenario**:

1. The system recognizes it is an admin to access the website. 
2. The admin types some letters of the email address he wants to contact in the search text area to get it in a quicker way.
3. The admin chooses the email address to contact among the ones displayed.
4. The admin clicks on a send button to actually send a mail with credentials.
5. The system displays a confirmation that the mail has been sent.

**Extensions**:

2a. The admin types wrong data in the search text area and therefore is not able to find the email address he wants to contact among the one displayed. 

3a. The email address is not present between the ones displayed because a mail has already been sent and first access has already been performed by the parent.
2b. Exploi

