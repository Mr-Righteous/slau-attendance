import csv
import random
from datetime import datetime

# Configuration
NUM_STUDENTS = 200
CURRENT_YEAR = datetime.now().year

# Departments and Courses
DEPARTMENTS = {
    'DptIE': 'Department of Informatics and Engineering'
}

COURSES = {
    'DptIE': [
        {'code': 'BAIT', 'name': 'Bachelor of Information Technology', 'duration': 3},
        {'code': 'BRAM', 'name': 'Bachelor of Records and Archives Management', 'duration': 3},
        {'code': 'BSTE', 'name': 'Bachelor of Science in Telecommunication Engineering', 'duration': 4},
        {'code': 'BSc.CE', 'name': 'Bachelor of Science in Computer Engineering', 'duration': 3},
        {'code': 'BACS', 'name': 'Bachelor of Computer Science', 'duration': 3},
    ]
}

# Enhanced names data with more variety
FIRST_NAMES = {
    'Uganda': ['David', 'James', 'John', 'Michael', 'Robert', 'Joseph', 'Daniel', 'Richard', 'Patrick', 'Paul',
                'Mary', 'Sarah', 'Grace', 'Alice', 'Joyce', 'Esther', 'Ruth', 'Mercy', 'Peace', 'Hope',
                'Brian', 'Simon', 'Thomas', 'Andrew', 'Mark', 'Stephen', 'Ann', 'Jane', 'Margaret', 'Elizabeth',
                'Emma', 'Olivia', 'Ava', 'Sophia', 'Charlotte', 'Mia', 'Amelia', 'Harper', 'Evelyn', 'Abigail',
                'Noah', 'Liam', 'William', 'Mason', 'James', 'Benjamin', 'Jacob', 'Michael', 'Elijah', 'Ethan'],
    'Kenya': ['Kevin', 'Brian', 'Victor', 'Samuel', 'Peter', 'Simon', 'Thomas', 'Andrew', 'Mark', 'Stephen',
               'Ann', 'Jane', 'Margaret', 'Elizabeth', 'Susan', 'Lucy', 'Catherine', 'Nancy', 'Irene', 'Winnie',
               'Dennis', 'Collins', 'Evans', 'Kennedy', 'Morris', 'Diana', 'Pamela', 'Mildred', 'Rose', 'Jackline',
               'Faith', 'Patricia', 'Dorcas', 'Brenda', 'Agnes', 'Eunice', 'Gladys', 'Lydia', 'Violet', 'Beatrice'],
    'Somalia': ['Ahmed', 'Mohamed', 'Ali', 'Hassan', 'Omar', 'Abdullahi', 'Abdi', 'Ibrahim', 'Yusuf', 'Musa',
               'Fatima', 'Aisha', 'Khadija', 'Maryam', 'Safia', 'Zainab', 'Hawa', 'Halima', 'Asha', 'Faduma',
               'Abdirahman', 'Farhan', 'Jama', 'Nur', 'Saida', 'Kawsar', 'Nasra', 'Hamdi', 'Sagal', 'Ifrah',
               'Leyla', 'Samira', 'Zahra', 'Naima', 'Filsan', 'Habibo', 'Asli', 'Bisharo', 'Farhiya', 'Guled'],
    'Sudan': ['Adam', 'Yasin', 'Kamal', 'Tariq', 'Nasir', 'Bashir', 'Hamza', 'Khalid', 'Rashid', 'Zubair',
                 'Amina', 'Mona', 'Salma', 'Nada', 'Rania', 'Hana', 'Lina', 'Noura', 'Sara', 'Yasmin',
                 'Mohammed', 'Omer', 'Ali', 'Mustafa', 'Hassan', 'Mariam', 'Awadia', 'Shaima', 'Isra', 'Rasha',
                 'Yara', 'Jana', 'Rim', 'Maya', 'Lamia', 'Nada', 'Rana', 'Sahar', 'Wafa', 'Bushra']
}

LAST_NAMES = {
    'Uganda': ['Mugisha', 'Tumwebaze', 'Ocen', 'Okello', 'Odongo', 'Opio', 'Opiyo', 'Akena', 'Okot', 'Ojok',
                'Akello', 'Adongo', 'Atoo', 'Apio', 'Acen', 'Aol', 'Auma', 'Atim', 'Aciro', 'Amony',
                'Kato', 'Ssemakula', 'Nsubuga', 'Lubwama', 'Mukasa', 'Wasswa', 'Kigozi', 'Kawooya', 'Nakitto', 'Nabukenya',
                'Mbabazi', 'Kansiime', 'Natukunda', 'Nalwoga', 'Nalubega', 'Nakafeero', 'Namyalo', 'Nalweyiso', 'Nabatanzi', 'Nabukeera'],
    'Kenya': ['Kipchoge', 'Korir', 'Kiptoo', 'Kibet', 'Kiprop', 'Kiplagat', 'Kemboi', 'Kipruto', 'Kosgei', 'Kipkemoi',
               'Chebet', 'Jepkosgei', 'Jepchirchir', 'Chepngetich', 'Jepleting', 'Kiprono', 'Rotich', 'Kipchumba', 'Kipngetich', 'Kipkoech',
               'Mwangi', 'Maina', 'Kamau', 'Njoroge', 'Nyambura', 'Wanjiru', 'Nyaguthii', 'Wambui', 'Atieno', 'Adhiambo',
               'Omondi', 'Otieno', 'Okoth', 'Ochieng', 'Odhiambo', 'Anyango', 'Achieng', 'Akinyi', 'Awuor', 'Apiyo'],
    'Somalia': ['Hussein', 'Mohamud', 'Abdi', 'Ali', 'Hassan', 'Osman', 'Farah', 'Adan', 'Dahir', 'Ibrahim',
               'Mohamed', 'Abdirahman', 'Barre', 'Duale', 'Hersi', 'Jama', 'Nur', 'Shire', 'Warsame', 'Yusuf',
               'Ahmed', 'Isse', 'Muse', 'Abdulle', 'Bulle', 'Diriye', 'Guelleh', 'Hirsi', 'Isak', 'Samatar',
               'Warsame', 'Gure', 'Bare', 'Hussein', 'Mahad', 'Nur', 'Osman', 'Roble', 'Salah', 'Yare'],
    'Sudan': ['Hassan', 'Mohamed', 'Ali', 'Ahmed', 'Ibrahim', 'Osman', 'Saeed', 'Abdallah', 'Khalil', 'Mustafa',
                 'Babiker', 'Eisa', 'Hamid', 'Idris', 'Mahdi', 'Nour', 'Salih', 'Taha', 'Younis', 'Zaki',
                 'Adam', 'Ali', 'Mohammed', 'Abdul', 'Khalifa', 'Mirghani', 'Siddig', 'Tahir', 'Yousif', 'Nasr',
                 'Mohamed', 'Ahmed', 'Ali', 'Hussein', 'Mahmoud', 'Omer', 'Suliman', 'Abdallah', 'Khalid', 'Ishaq']
}

GENDERS = ['male', 'female']
COUNTRIES = ['Uganda', 'Kenya', 'Somalia', 'Sudan']
COUNTRY_CODES = {
    'Uganda': '+256',
    'Kenya': '+254', 
    'Somalia': '+252',
    'Sudan': '+249'
}

INTAKES = ['D', 'W']
NATIONALITY_CODES = {
    'Uganda': 'U',
    'Kenya': 'K', 
    'Somalia': 'S',
    'Sudan': 'N'
}

def generate_phone_number(country, used_numbers):
    prefix = COUNTRY_CODES[country]
    while True:
        number = ''.join([str(random.randint(0, 9)) for _ in range(9)])
        full_number = f"{prefix}{number}"
        if full_number not in used_numbers:
            used_numbers.add(full_number)
            return full_number

def generate_registration_number(course_code, intake_year, intake_type, nationality_code, sequence):
    return f"{course_code}/{intake_year}{intake_type}/{nationality_code}/{sequence:04d}"

def generate_student_data():
    students = []
    it_courses = COURSES['DptIE']
    academic_years = [2023, 2024]
    
    # Track used combinations to ensure uniqueness
    used_emails = set()
    used_reg_numbers = set()
    used_phones = set()
    sequence_numbers = {}
    
    for i in range(1, NUM_STUDENTS + 1):
        nationality = random.choice(COUNTRIES)
        course = random.choice(it_courses)
        course_code = course['code']
        dept_code = 'DptIE'
        
        intake_year = random.choice(academic_years)
        intake_type = random.choice(INTAKES)
        nationality_code = NATIONALITY_CODES[nationality]
        
        # Generate unique sequence number
        key = f"{course_code}/{intake_year}{intake_type}/{nationality_code}"
        if key not in sequence_numbers:
            sequence_numbers[key] = 1
        sequence_num = sequence_numbers[key]
        sequence_numbers[key] += 1
        
        # Generate registration number
        reg_number = generate_registration_number(course_code, intake_year, intake_type, nationality_code, sequence_num)
        
        # Generate unique name and email
        attempts = 0
        while attempts < 10:  # Try up to 10 times to generate unique email
            first_name = random.choice(FIRST_NAMES[nationality])
            last_name = random.choice(LAST_NAMES[nationality])
            full_name = f"{first_name} {last_name}"
            
            # Generate email with potential disambiguation
            base_email = f"{first_name.lower()}.{last_name.lower()}"
            email = f"{base_email}@student.slu.ac.ug"
            
            # If email already used in this batch, add a number
            if email in used_emails:
                email = f"{base_email}{random.randint(1, 99)}@student.slu.ac.ug"
            
            if email not in used_emails:
                break
            attempts += 1
        
        if attempts >= 10:
            continue  # Skip this student if we can't generate unique email
        
        # Student details
        gender = random.choice(GENDERS)
        years_since_intake = CURRENT_YEAR - intake_year
        max_year = min(course['duration'], 4)
        current_year = min(max(1, years_since_intake + 1), max_year)
        current_semester = random.randint(1, 2)
        academic_year = CURRENT_YEAR
        
        # Generate unique phone number
        phone = generate_phone_number(nationality, used_phones)
        
        # Add to used trackers
        used_emails.add(email)
        used_reg_numbers.add(reg_number)
        
        students.append({
            'registration_number': reg_number,
            'name': full_name,
            'email': email,
            'department_code': dept_code,
            'course_code': course_code,
            'current_year': current_year,
            'current_semester': current_semester,
            'academic_year': academic_year,
            'gender': gender,
            'phone': phone,
            'nationality': nationality
        })
    
    return students

def save_to_csv(students, filename='students_data.csv'):
    fieldnames = [
        'registration_number', 'name', 'email', 'department_code', 'course_code',
        'current_year', 'current_semester', 'academic_year', 'gender', 'phone', 'nationality'
    ]
    
    with open(filename, 'w', newline='', encoding='utf-8') as csvfile:
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(students)
    
    print(f"Generated {len(students)} unique student records in {filename}")
    
    # Print sample records
    print("\nSample records:")
    for i in range(min(5, len(students))):
        print(f"{students[i]['registration_number']} - {students[i]['name']} - {students[i]['email']}")

if __name__ == "__main__":
    print("Generating unique student data for IT courses...")
    students = generate_student_data()
    save_to_csv(students)
    print("\nDone! File is ready for import.")