import speech_recognition as sr
import os
import subprocess
import sys

# Ensure UTF-8 output for special characters
sys.stdout.reconfigure(encoding='utf-8')

def convert_to_clean_wav(input_path, output_path):
    """
    Uses FFmpeg to convert input to 16kHz Mono WAV.
    Silences FFmpeg output.
    """
    command = [
        'ffmpeg',
        '-y',               # Overwrite output without asking
        '-i', input_path,   # Input file
        '-vn',              # Disable video
        '-acodec', 'pcm_s16le', 
        '-ac', '1',         # Mono
        '-ar', '16000',     # 16kHz
        output_path
    ]
    
    try:
        # Run ffmpeg silently
        subprocess.check_call(command, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        return True
    except Exception:
        return False

def transcribe_file(file_path):
    recognizer = sr.Recognizer()
    
    # Create temp filename based on original
    temp_wav = file_path + ".temp.wav"
    
    if not os.path.exists(file_path):
        return "Error: File not found"

    # 1. Convert to clean WAV (Handles mp3, m4a, ogg, etc.)
    if not convert_to_clean_wav(file_path, temp_wav):
        return "Error: File conversion failed (Check FFmpeg)"

    # 2. Transcribe
    result_text = ""
    try:
        with sr.AudioFile(temp_wav) as source:
            audio_data = recognizer.record(source)
            # Perform transcription
            result_text = recognizer.recognize_google(audio_data)

    except sr.UnknownValueError:
        result_text = "Error: Could not understand audio"
    except sr.RequestError as e:
        result_text = f"Error: API unavailable - {str(e)}"
    except Exception as e:
        result_text = f"Error: System error - {str(e)}"
    finally:
        # 3. Cleanup temp file
        if os.path.exists(temp_wav):
            os.remove(temp_wav)
            
    return result_text

if __name__ == "__main__":
    # Get filename from command line argument (passed by PHP)
    if len(sys.argv) > 1:
        input_file = sys.argv[1]
        transcription = transcribe_file(input_file)
        
        # Print ONLY the text result. 
        # This is exactly what PHP's shell_exec() will capture.
        print(transcription)
    else:
        print("Error: No input file provided")