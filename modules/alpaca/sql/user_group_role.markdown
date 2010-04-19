# user, group, role relationships

## Example
USER	GROUP	ROLE
1   -> 	1   -> 	owner
1   -> 	2   -> 	admin
1   -> 	3   -> 	owner


## Tables Schema
USER: 				user_id
					1
					
GROUP:				group_id
					1
					2
					3

USER_GROUP:			user_id,	group_id
					1			1
					1			2
					1			3
					
ROLE: 				role_id,	name,			desc
					10			group_member	
					12			group_owner		
					13			group_admin

USER_GROUP_ROLE:	user_id,	group_id,		role_id
					1			1				10
					1			2				12
					1			3				11