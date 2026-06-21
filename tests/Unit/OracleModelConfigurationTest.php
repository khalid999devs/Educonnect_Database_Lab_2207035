<?php

namespace Tests\Unit;

use App\Models\AcademicDocument;
use App\Models\AcademicField;
use App\Models\AcademicTask;
use App\Models\AuditLog;
use App\Models\ExtractedDocumentData;
use App\Models\ResearchCollection;
use App\Models\ResearchTopic;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Review;
use App\Models\SavedResource;
use App\Models\SavedTemplate;
use App\Models\Student;
use App\Models\StudentPreference;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\TemplatePurchase;
use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use Tests\TestCase;

class OracleModelConfigurationTest extends TestCase
{
    #[DataProvider('modelTables')]
    public function test_models_use_oracle_connection_and_explicit_tables(string $modelClass, string $table): void
    {
        $model = new $modelClass;

        $this->assertSame('oracle', $model->getConnectionName());
        $this->assertSame($table, $model->getTable());
    }

    /**
     * @return array<string, array{class-string, string}>
     */
    public static function modelTables(): array
    {
        return [
            'users' => [User::class, 'users'],
            'universities' => [University::class, 'universities'],
            'academic fields' => [AcademicField::class, 'academic_fields'],
            'students' => [Student::class, 'students'],
            'student preferences' => [StudentPreference::class, 'student_preferences'],
            'academic documents' => [AcademicDocument::class, 'academic_documents'],
            'extracted document data' => [ExtractedDocumentData::class, 'extracted_document_data'],
            'academic tasks' => [AcademicTask::class, 'academic_tasks'],
            'tool categories' => [ToolCategory::class, 'tool_categories'],
            'tools' => [Tool::class, 'tools'],
            'resource categories' => [ResourceCategory::class, 'resource_categories'],
            'resources' => [Resource::class, 'resources'],
            'template categories' => [TemplateCategory::class, 'template_categories'],
            'templates' => [Template::class, 'templates'],
            'saved resources' => [SavedResource::class, 'saved_resources'],
            'saved templates' => [SavedTemplate::class, 'saved_templates'],
            'template purchases' => [TemplatePurchase::class, 'template_purchases'],
            'research topics' => [ResearchTopic::class, 'research_topics'],
            'research collections' => [ResearchCollection::class, 'research_collections'],
            'reviews' => [Review::class, 'reviews'],
            'audit logs' => [AuditLog::class, 'audit_logs'],
        ];
    }

    public function test_reviews_use_the_configured_polymorphic_map(): void
    {
        $this->assertSame(Resource::class, Relation::getMorphedModel('RESOURCE'));
        $this->assertSame(Template::class, Relation::getMorphedModel('TEMPLATE'));
        $this->assertSame(Tool::class, Relation::getMorphedModel('TOOL'));

        $this->assertSame(MorphTo::class, (string) (new ReflectionMethod(Review::class, 'reviewable'))->getReturnType());
        $this->assertSame(MorphMany::class, (string) (new ReflectionMethod(Resource::class, 'reviews'))->getReturnType());
        $this->assertSame(MorphMany::class, (string) (new ReflectionMethod(Template::class, 'reviews'))->getReturnType());
        $this->assertSame(MorphMany::class, (string) (new ReflectionMethod(Tool::class, 'reviews'))->getReturnType());
    }
}
