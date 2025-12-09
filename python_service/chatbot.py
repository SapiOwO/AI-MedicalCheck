"""
Healthcare AI Chatbot - Expert System (Based on Temporary/ai_service/model.py)
Uses rule-based diagnosis with emotion integration
"""

import re
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent


class MedicalKnowledgeBase:
    """Knowledge base with disease information and diagnostic rules"""
    
    def __init__(self):
        self.diagnosis_info = {
            "Common Cold": {"description": "A mild viral infection dealing with nose and throat.", "advice": "Rest, stay hydrated, take Vitamin C."},
            "Influenza": {"description": "A contagious respiratory illness (The Flu).", "advice": "Get plenty of rest, drink fluids, monitor fever."},
            "COVID-19": {"description": "An infectious disease caused by the SARS-CoV-2 virus.", "advice": "Isolate yourself, wear a mask, seek help if breathing is hard."},
            "Bronchitis": {"description": "Inflammation of the lining of your bronchial tubes.", "advice": "Rest, drink fluids, use a humidifier."},
            "Pneumonia": {"description": "Infection that inflames air sacs in one or both lungs.", "advice": "Requires medical diagnosis and treatment, often with antibiotics."},
            "Asthma": {"description": "A chronic condition where airways narrow and swell.", "advice": "Use prescribed inhalers. Avoid triggers like dust/smoke."},
            "Allergic Rhinitis": {"description": "Allergic response causing sneezing and congestion.", "advice": "Avoid allergens (pollen, dust). Antihistamines may help."},
            "Hypertension": {"description": "High blood pressure (Hypertension).", "advice": "Lifestyle changes (diet, exercise) and prescribed medication. Monitor BP."},
            "Heart Disease": {"description": "Conditions affecting heart structure/function.", "advice": "This is serious. Consult a cardiologist for evaluation immediately."},
            "Diabetes": {"description": "Metabolic disease causing high blood sugar.", "advice": "Manage diet/sugar intake. Consult an endocrinologist."},
            "Gastroenteritis": {"description": "Inflammation of stomach/intestines (Stomach Flu).", "advice": "Hydrate with electrolytes. Bland diet (BRAT)."},
            "Migraine": {"description": "Severe throbbing headache, often with nausea.", "advice": "Rest in a quiet, dark room. Pain relief medication."},
            "Depression": {"description": "A mental health disorder causing persistent sadness.", "advice": "Seek professional mental health support. Talk therapy and medication can help."},
            "Anxiety Disorders": {"description": "Mental health condition causing excessive worry.", "advice": "Practice relaxation techniques. Consider cognitive behavioral therapy."},
            "Healthy": {"description": "No significant symptoms detected.", "advice": "Continue to maintain a healthy lifestyle."},
            "Unknown": {"description": "Symptoms are unclear or conflicting.", "advice": "Please consult a healthcare professional for a checkup."}
        }

        # EXPERT RULES (Forward Chaining Logic)
        self.rules = {
            "Influenza": {
                "required": ["Fever", "Fatigue", "Cough"],
                "optional": ["Difficulty Breathing"], 
                "forbidden": []
            },
            "Pneumonia": {
                "required": ["Fever", "Cough", "Difficulty Breathing"],
                "optional": ["Fatigue"],
                "forbidden": []
            },
            "Common Cold": {
                "required": ["Cough"],
                "optional": ["Fatigue", "Difficulty Breathing"],
                "forbidden": ["Fever", "High Blood Pressure"]
            },
            "Bronchitis": {
                "required": ["Cough", "Fever"],
                "optional": ["Fatigue", "Difficulty Breathing"],
                "forbidden": []
            },
            "Asthma": {
                "required": ["Difficulty Breathing", "Cough"],
                "optional": ["Fatigue"],
                "forbidden": ["Fever"]
            },
            "Allergic Rhinitis": {
                "required": ["Difficulty Breathing"],
                "optional": ["Cough", "Fatigue"],
                "forbidden": ["Fever", "High Blood Pressure"]
            },
            "Hypertension": {
                "required": ["High Blood Pressure"],
                "optional": ["Fatigue"],
                "forbidden": ["Fever"]
            },
            "Heart Disease": {
                "required": [],
                "optional": ["Difficulty Breathing", "Fatigue"],
                "triggers": ["High Blood Pressure", "High Cholesterol"],
                "forbidden": ["Fever"]
            },
            "Diabetes": {
                "required": ["Fatigue"],
                "optional": [],
                "forbidden": ["Fever", "Cough"]
            },
            "Gastroenteritis": {
                "required": ["Fever", "Fatigue"], 
                "optional": [],
                "forbidden": ["Cough", "Difficulty Breathing"]
            },
            "Depression": {
                "required": ["Fatigue"],
                "optional": [],
                "forbidden": ["Fever", "Cough"],
                "emotion_trigger": ["sad"]
            },
            "Anxiety Disorders": {
                "required": [],
                "optional": ["Fatigue", "Difficulty Breathing"],
                "forbidden": ["Fever"],
                "emotion_trigger": ["fear", "angry"]
            }
        }

    def get_advice(self, disease):
        return self.diagnosis_info.get(disease, self.diagnosis_info["Unknown"])


class ExpertEngine:
    """Expert system engine for diagnosis scoring"""
    
    def __init__(self, kb: MedicalKnowledgeBase):
        self.kb = kb

    def diagnose(self, profile):
        """Score diseases based on symptoms"""
        scores = {}
        
        active_symptoms = set()
        for k, v in profile.items():
            if v == "Yes":
                active_symptoms.add(k)
            if k == "Blood Pressure" and v == "High":
                active_symptoms.add("High Blood Pressure")
            if k == "Cholesterol Level" and v == "High":
                active_symptoms.add("High Cholesterol")
        
        emotion = profile.get("Emotion", "Neutral").lower()
        
        for disease, rule in self.kb.rules.items():
            score = 0
            
            triggers = rule.get("triggers", [])
            for t in triggers:
                if t in active_symptoms:
                    score += 5
            
            emotion_triggers = rule.get("emotion_trigger", [])
            if emotion in emotion_triggers:
                score += 3
            
            for req in rule.get("required", []):
                if req in active_symptoms:
                    score += 5
                else:
                    score -= 5
            
            for opt in rule.get("optional", []):
                if opt in active_symptoms:
                    score += 1
            
            for forb in rule.get("forbidden", []):
                if forb in active_symptoms:
                    score -= 10
            
            scores[disease] = score
        
        valid_diseases = {k: v for k, v in scores.items() if v > 0}
        
        if not valid_diseases:
            return "Unknown", 0.0, scores
        
        winner = max(valid_diseases, key=valid_diseases.get)
        max_score = valid_diseases[winner]
        total_score = sum(valid_diseases.values())
        confidence = max_score / total_score if total_score > 0 else 0.0
        
        return winner, confidence, scores


class HealthcareModel:
    """Main healthcare chatbot model"""
    
    def __init__(self):
        print("Initializing Healthcare AI Chatbot...")
        self.kb = MedicalKnowledgeBase()
        self.engine = ExpertEngine(self.kb)
        print("Healthcare AI ready!")
    
    def get_initial_greeting(self, profile):
        """Generate initial greeting and start asking questions"""
        emotion = profile.get("Emotion", "Neutral")
        age = profile.get("Age", "")
        
        greeting = "Hello! I'm your **MediSight AI Health Assistant**.\n\n"
        
        if emotion and emotion.lower() not in ["neutral", "unknown", ""]:
            emotion_responses = {
                "sad": "I notice you might be feeling a bit down. ",
                "happy": "Great to see you! ",
                "angry": "I sense some tension. Let's take this step by step. ",
                "fear": "Don't worry, I'm here to help you. ",
            }
            if emotion.lower() in emotion_responses:
                greeting += emotion_responses[emotion.lower()]
        
        if age and str(age) not in ["0", ""]:
            try:
                if int(age) > 0:
                    greeting += f"I see you're {age} years old. "
            except:
                pass
        
        greeting += "\n\nI'll ask you a few questions about your symptoms.\n"
        greeting += "Please answer **YES** or **NO** to each question.\n\n"
        greeting += "**Question 1**: Do you have a **Fever**?"
        
        return greeting
    
    def recommend(self, profile, message, context=""):
        """Process message and return response"""
        lower_msg = message.lower().strip()
        
        # --- 1. Parse YES/NO answers from context ---
        # Find what symptom was being asked about in the previous message (context)
        symptom_asked = None
        if context:
            # Look for pattern: **Fever**? anywhere in the context (last occurrence)
            matches = re.findall(r'\*\*([A-Za-z ]+)\*\*\s*\?', context)
            if matches:
                symptom_asked = matches[-1]  # Get the last match (the actual question)
            else:
                # Try to find symptom keywords in context  
                for symptom in ["Fever", "Cough", "Fatigue", "Difficulty Breathing", "Blood Pressure", "Cholesterol"]:
                    if symptom.lower() in context.lower() and "?" in context:
                        symptom_asked = symptom
                        break
        
        # Check if user answered YES or NO
        is_yes = bool(re.search(r'^(yes|ya|yep|yeah|yup|iya|iye|y)$', lower_msg)) or \
                 bool(re.search(r'\b(yes|ya|yep|yeah|yup|iya|i do|i have)\b', lower_msg))
        is_no = bool(re.search(r'^(no|nope|nah|tidak|gak|enggak|n|tdk)$', lower_msg)) or \
                bool(re.search(r'\b(no|nope|nah|tidak|gak|enggak|i don\'t|i dont)\b', lower_msg))
        
        # Apply answer to the asked symptom
        if symptom_asked:
            if is_yes:
                if "blood pressure" in symptom_asked.lower():
                    profile["Blood Pressure"] = "High"
                elif "cholesterol" in symptom_asked.lower():
                    profile["Cholesterol Level"] = "High"
                else:
                    profile[symptom_asked] = "Yes"
            elif is_no:
                if "blood pressure" in symptom_asked.lower():
                    profile["Blood Pressure"] = "Normal"
                elif "cholesterol" in symptom_asked.lower():
                    profile["Cholesterol Level"] = "Normal"
                else:
                    profile[symptom_asked] = "No"
        
        # --- 2. Also check for direct symptom mentions ---
        symptom_keywords = {
            "Fever": ["fever", "feverish", "hot", "chills", "demam", "panas"],
            "Cough": ["cough", "coughing", "batuk"],
            "Fatigue": ["fatigue", "tired", "exhausted", "weak", "lelah", "capek", "lemas"],
            "Difficulty Breathing": ["breath", "breathing", "wheeze", "sesak", "napas", "asma"]
        }
        
        for symptom, keywords in symptom_keywords.items():
            for kw in keywords:
                if kw in lower_msg:
                    # Check for negation
                    neg_pattern = r'\b(no|not|never|don\'t|dont|tidak|gak|tanpa)\b.{0,15}' + kw
                    if re.search(neg_pattern, lower_msg):
                        profile[symptom] = "No"  # Always update, not just when None
                    else:
                        profile[symptom] = "Yes"
                    break
        
        # Specific check for "difficulty breathing" phrase
        if "difficulty breathing" in lower_msg:
            neg_db = re.search(r'\b(no|not|never|don\'t|dont|tidak|gak)\b.{0,20}difficulty', lower_msg)
            if neg_db:
                profile["Difficulty Breathing"] = "No"
            else:
                profile["Difficulty Breathing"] = "Yes"
        
        # Handle Blood Pressure and Cholesterol with negation support
        if "blood pressure" in lower_msg:
            neg_bp = re.search(r'\b(no|not|never|don\'t|dont|tidak|gak)\b.{0,15}(high|blood pressure)', lower_msg)
            if neg_bp:
                profile["Blood Pressure"] = "Normal"
            elif "high blood pressure" in lower_msg or "hipertensi" in lower_msg:
                profile["Blood Pressure"] = "High"
        
        if "cholesterol" in lower_msg:
            neg_chol = re.search(r'\b(no|not|never|don\'t|dont|tidak|gak)\b.{0,15}(high|cholesterol)', lower_msg)
            if neg_chol:
                profile["Cholesterol Level"] = "Normal"
            elif "high cholesterol" in lower_msg or "kolesterol tinggi" in lower_msg:
                profile["Cholesterol Level"] = "High"
        
        # --- 3. Determine next action ---
        main_symptoms = ["Fever", "Cough", "Fatigue", "Difficulty Breathing"]
        
        # Count answered questions
        answered = []
        unanswered = []
        for s in main_symptoms:
            if profile.get(s) in ["Yes", "No"]:
                answered.append(s)
            else:
                unanswered.append(s)
        
        has_symptoms = any(profile.get(s) == "Yes" for s in main_symptoms)
        has_vitals = profile.get("Blood Pressure") == "High" or profile.get("Cholesterol Level") == "High"
        
        # --- 4. If we have enough info, give diagnosis ---
        if len(answered) >= 4 or (len(answered) >= 2 and has_symptoms):
            disease, confidence, scores = self.engine.diagnose(profile)
            
            # Check if diagnosis is clear
            sorted_scores = sorted(scores.values(), reverse=True)
            top_score = sorted_scores[0] if sorted_scores else 0
            runner_up = sorted_scores[1] if len(sorted_scores) > 1 else 0
            is_ambiguous = (top_score - runner_up) < 5 and top_score < 15
            
            threshold = 0.5 if has_vitals else 0.6
            
            if (confidence > threshold and not is_ambiguous) or len(answered) >= 4:
                info = self.kb.get_advice(disease)
                response = f"Based on your symptoms, my analysis suggests: **{disease}** ({confidence*100:.1f}% Match).\n\n"
                response += f"**Description**: {info['description']}\n\n"
                response += f"**Advice**: {info['advice']}"
                return response, profile
        
        # --- 5. Ask next question ---
        question_num = len(answered) + 1
        
        if unanswered:
            next_symptom = unanswered[0]
            
            if next_symptom == "Difficulty Breathing":
                return f"**Question {question_num}**: Do you have **Difficulty Breathing**?", profile
            else:
                return f"**Question {question_num}**: Do you have a **{next_symptom}**?", profile
        
        # All symptoms answered - check vitals
        if profile.get("Blood Pressure") is None:
            return f"**Question {question_num}**: Do you have **high blood pressure**?", profile
        
        if profile.get("Cholesterol Level") is None:
            return f"**Question {question_num}**: Do you have **high cholesterol**?", profile
        
        # Give final diagnosis
        disease, confidence, scores = self.engine.diagnose(profile)
        info = self.kb.get_advice(disease)
        
        if disease == "Unknown" or confidence < 0.3:
            return "Based on your symptoms, I cannot determine a specific condition. Please consult a healthcare professional.", profile
        
        response = f"Based on your symptoms, my analysis suggests: **{disease}** ({confidence*100:.1f}% Match).\n\n"
        response += f"**Description**: {info['description']}\n\n"
        response += f"**Advice**: {info['advice']}"
        return response, profile
    
    def generate_random_profile(self):
        """Generate empty profile for new session"""
        return {
            "Fever": None,
            "Cough": None,
            "Fatigue": None,
            "Difficulty Breathing": None,
            "Age": 30,
            "Gender": "Male",
            "Blood Pressure": None,
            "Cholesterol Level": None,
            "Outcome Variable": "Negative",
            "Emotion": "Neutral"
        }
